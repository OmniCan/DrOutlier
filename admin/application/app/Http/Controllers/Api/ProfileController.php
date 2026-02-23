<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProfileController extends Controller
{


    public function submitProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:40',
            'lastname' => 'nullable|string|max:40',
            'mobile' => 'nullable|string|max:40',
            'country_code' => 'nullable|string|max:10',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        // Update user fields
        $user->firstname = $request->firstname;

        if ($request->has('lastname')) {
            $user->lastname = $request->lastname;
        }

        if ($request->has('mobile')) {
            $user->mobile = $request->mobile;
        }

        if ($request->has('country_code')) {
            $user->country_code = $request->country_code;
        }

        if ($request->hasFile('image'))
        {
            $path = getFilePath('userProfile');
            fileManager()->removeFile($path.'/'.$user->image);
            $directory = $user->username."/". $user->id;
            $path = getFilePath('userProfile').'/'.$directory;
            $filename = $directory.'/'.fileUploader($request->image, $path, getFileSize('userProfile'));
            $user->image = $filename;
        }

        $user->save();

        $notify[] = ['success', 'Profile has been updated successfully'];

        return response()->json([
            'success' => true,
            'message' => ['success' => $notify],
        ]);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        $general = gs();
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changes successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }

    public function imageUpdate(Request $request)
    {
        $this->validate($request, [
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);
        $user = auth()->user();
        if ($request->hasFile('image'))
        {
            $path = getFilePath('userProfile');
            fileManager()->removeFile($path.'/'.$user->image);
            $directory = $user->username."/". $user->id;
            $path = getFilePath('userProfile').'/'.$directory;
            $filename = $directory.'/'.fileUploader($request->image, $path, getFileSize('userProfile'));
            $user->image = $filename;
        }
        $user->save();
        $notify[] = ['success', 'Profile image has been updated successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function tokenStore(Request $request){

        User::where('id' , $request->id)->update(['fcm_token' => $request->token]);

        return response()->json([
            'message'=>'Added Success!!',
        ]);
    }
}
