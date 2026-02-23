<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Blog;
use App\Models\Spotter;
use App\Models\Osce;
use App\Models\Munchie;
use App\Models\WatchAndLearn;
use App\Models\Basic;

class UserController extends Controller
{
    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            $notify[] = 'You\'ve already completed your profile';
            return response()->json([
                'remark'=>'already_completed',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }
        $validator = Validator::make($request->all(), [
            'firstname'=>'required',
            'lastname'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }


        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country'=>@$user->address->country,
            'address'=>$request->address,
            'state'=>$request->state,
            'zip'=>$request->zip,
            'city'=>$request->city,
        ];
        $user->reg_step = 1;
        $user->save();

        $notify[] = 'Profile completed successfully';
        return response()->json([
            'remark'=>'profile_completed',
            'status'=>'success',
            'message'=>['success'=>$notify],
        ]);
    }


    public function depositHistory(Request $request)
    {
        $deposits = auth()->user()->deposits();
        if ($request->search) {
            $deposits = $deposits->where('trx',$request->search);
        }
        $deposits = $deposits->with(['gateway'])->orderBy('id','desc')->paginate(getPaginate());
        $notify[] = 'Deposit data';
        return response()->json([
            'remark'=>'deposits',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'deposits'=>$deposits
            ]
        ]);
    }

    public function transactions(Request $request)
    {
        $remarks = Transaction::distinct('remark')->get('remark');
        $transactions = Transaction::where('user_id',auth()->id());

        if ($request->search) {
            $transactions = $transactions->where('trx',$request->search);
        }


        if ($request->type) {
            $type = $request->type == 'plus' ? '+' : '-';
            $transactions = $transactions->where('trx_type',$type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark',$request->remark);
        }

        $transactions = $transactions->orderBy('id','desc')->paginate(getPaginate());
        $notify[] = 'Transactions data';
        return response()->json([
            'remark'=>'transactions',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'transactions'=>$transactions,
                'remarks'=>$remarks,
            ]
        ]);
    }

    public function submitProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname'=>'required',
            'lastname'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country'=>@$user->address->country,
            'address'=>$request->address,
            'state'=>$request->state,
            'zip'=>$request->zip,
            'city'=>$request->city,
        ];
        $user->save();

        $notify[] = 'Profile has been updated successfully';
        return response()->json([
            'remark'=>'profile_updated',
            'status'=>'success',
            'message'=>['success'=>$notify],
        ]);
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = 'Password changed successfully';
            return response()->json([
                'remark'=>'password_changed',
                'status'=>'success',
                'message'=>['success'=>$notify],
            ]);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }
    }




    public function userDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $user = User::where('id' , $request->id)->first();

        return response()->json([
            'status'=>'success',
            'data'=>[
                'user'=>$user,
            ]
        ]);
    }

    public function requestTokenGoogle(Request $request) {

        $user = Socialite::driver('google')->stateless()->userFromToken($request->token);


            $email = $user->getEmail();
            $emailParts = explode('@', $email);
            $username = $emailParts[0];

            $firstName = $user->offsetGet('given_name');

            if($firstName != null){
                $name = $firstName;
            }else{
                $name = $username;
            }
        $userFromDb = User::firstOrCreate(
            ['email' => $user->getEmail()],
            [
                'first_name' => $request->first_name ?? $name,
                'last_name' => $request->last_name ?? '',
                'image' => $user->getAvatar(),
                'username' => $request->username ?? $name,
            ]
        );

        $tokenResult = $userFromDb->createToken('auth_token')->plainTextToken;


        $response = ['tokenResult' => $tokenResult, 'message' => 'Google Login/Signup Successful','userFromDb' => $userFromDb];
        return response($response, 200);
    }


    public function deleteAccount(Request $request){

        $user = User::find($request->id);
        $user->delete();

        return response()->json([
            'message'=>['success'=>'User Deleted Success!!'],
        ]);

    }

    public function userDetailsData(){

        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Ensure all fields are returned with null safety
            // Construct full image URL if image exists
            $imageUrl = null;
            if ($user->image) {
                $imageUrl = 'assets/images/user/profile/' . $user->image;
            } elseif ($user->avatar) {
                $imageUrl = $user->avatar; // Google avatar is already a full URL
            }

            $userData = [
                'id' => $user->id ?? null,
                'firstname' => $user->firstname ?? null,
                'lastname' => $user->lastname ?? null,
                'email' => $user->email ?? null,
                'mobile' => $user->mobile ?? null,
                'country_code' => $user->country_code ?? null,
                'image' => $imageUrl,
                'avatar' => $user->avatar ?? null,
                'status' => $user->status ?? null,
                'created_at' => $user->created_at ?? null,
                'updated_at' => $user->updated_at ?? null,
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'list' => $userData,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Search(Request $request){

        $title = $request->input('title');

        $notes = Blog::with('categories')->where('title', 'like', "%{$title}%")->get();
        $spotters = Spotter::with('categories')->where('title', 'like', "%{$title}%")->get();
        $osce = Osce::where('title', 'like', "%{$title}%")->get();
        $munchies = Munchie::with('categories')->where('title', 'like', "%{$title}%")->get();
        $whatchandlearn = WatchAndLearn::with('categories')->where('title', 'like', "%{$title}%")->get();
        $basics = Basic::with('categories')->where('title', 'like', "%{$title}%")->get();

        $datalist = [
            "notes" => $notes,
            "spotters" => $spotters,
            "osce" => $osce,
            "munchies" => $munchies,
            "whatchandlearn" => $whatchandlearn,
            "basics" => $basics,
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'datalist' => $datalist,
            ]
        ]);

    }
}
