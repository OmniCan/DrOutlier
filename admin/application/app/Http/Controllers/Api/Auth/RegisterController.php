<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Mail\UserVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('registration.status')->except('registrationNotAllowed');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = GeneralSetting::first();
        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $validate = Validator::make($data, [
            'firstname' => 'required',
            'email' => 'required|string|email|unique:users,email',
            'password' => ['required','confirmed',$passwordValidation], 
        ]);
        return $validate;
    }



    
    public function register(Request $request)
    { 
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
    
        $user = User::where('email', strtolower(trim($request->email)))->first();
    
        
        if ($user && $user->status == 0 && is_null($user->email_verified_at)) {
            // $user->delete();  
            $user = null; 

            
        }
    
        if (!$user) {
            $user = new User();
            $user->firstname = $request->firstname;
            $user->email = strtolower(trim($request->email));
            $user->password = Hash::make($request->password);  
            $user->status = 0;  
    
            $user->verification_token = Str::random(32); 
            $user->save();
    
            Mail::to($user->email)->send(new UserVerificationMail($user));
    
            return response()->json([
                'remark' => 'email_verification_sent',
                'status' => 'success',
                'message' => ['Please check your email to verify your account.'],
            ]);
        }
    
        if ($user->status == 0 && is_null($user->email_verified_at)) {
            $user->verification_token = Str::random(32); 
            $user->save();
    
            Mail::to($user->email)->send(new UserVerificationMail($user));
    
            return response()->json([
                'remark' => 'verification_email_sent',
                'status' => 'success',
                'message' => ['Verification email has been resent to your email.'],
            ]);
        }

        if ($user) {
            // Check if the user is already verified and active
            if ($user->status == 1 && !is_null($user->email_verified_at)) {
                return response()->json([
                    'remark' => 'email_exists',
                    'status' => 'error',
                    'message' => ['Your email is already registered and verified. You can login now.'],
                ]);
            }
    
        // if ($user->status == 1 && !is_null($user->email_verified_at)) {
        //     return response()->json([
        //         'remark' => 'already_verified',
        //         'status' => 'error',
        //         'message' => ['Your email is already verified. You can login now.....'],
        //     ]);
         }
    }
    
    
    


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $general = GeneralSetting::first();
 
        $user = new User();
        $user->firstname = $data['firstname'];
        $user->email = strtolower(trim($data['email']));
        $user->password = Hash::make($data['password']);  
        $user->status = 0; 
        $user->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail',$user->id);
        $adminNotification->save();


          
        return $user;
    }

  

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();
    
        if (!$user) {
            return response()->json([
                'remark' => 'error',
                'status' => 'error',
                'message' => ['Invalid verification token'],
            ]);
        }
    
        $user->status = 1;
        $user->email_verified_at = now();
        $user->verification_token = null;
    
        $user->save();
    
        return response()->json([
            'remark' => 'verified',
            'status' => 'success',
            'message' => ['Email verified successfully. Now you can visit our site for login...']
        ]);
    }   
}