<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\job\SendPasswordResetEmail;
use App\Models\GeneralSetting;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordResetCode; 
use Illuminate\Support\Facades\Mail; 

class ForgotPasswordController extends Controller
{
   

    public function sendResetCodeEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $fieldType = $this->findFieldType();
        $user = User::where($fieldType, $request->value)->first();

        if (!$user) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => ['Couldn\'t find any account with this information']],
            ]);
        }

        PasswordReset::where('email', $user->email)->delete();

        $code = verificationCode(6);

        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = \Carbon\Carbon::now();
        $password->save();

        try {
       
            Mail::to($user->email)->send(new PasswordResetCode($code));

         
            \Log::info('Email sent successfully to: ' . $user->email);
        } catch (\Exception $e) {
         
            \Log::error('Error sending email: ' . $e->getMessage());

            return response()->json([
                'remark' => 'email_error',
                'status' => 'error',
                'message' => ['error' => ['Failed to send email']],
            ]);
        }

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();

        notify($user, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => @$userBrowserInfo['os_platform'],
            'browser' => @$userBrowserInfo['browser'],
            'ip' => @$userIpInfo['ip'],
            'time' => @$userIpInfo['time']
        ], ['email']);

        return response()->json([
            'remark' => 'code_sent',
            'status' => 'success',
            'message' => ['success' => ['Verification code sent to mail']],
            'data' => [
                'code' => $code,
                'email' => $user->email
            ]
        ]);
    }

    private function findFieldType()
    {
        $input = request()->input('value');
        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $input]);
        return $fieldType;
    }
    
   public function sendEmail($to, $subject, $code)
   {  
        try {
            
                Mail::to($to)->send(new PasswordResetCode($code));  
                return true;  
            } catch (\Exception $e) {
        
                \Log::error('Error sending email: ' . $e->getMessage());
                return false;
           }
    }

    

    

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }
        $code =  $request->code;

        if (PasswordReset::where('token', $code)->where('email', $request->email)->count() != 1) {
            $notify[] = 'Verification code doesn\'t match';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

        $response[] = 'You can change your password.';
        return response()->json([
            'remark'=>'verified',
            'status'=>'success',
            'message'=>['success'=>$response],
        ]);
    }

    public function reset(Request $request)
    {

        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }


        $reset = PasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $response[] = 'Invalid verification code.';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['success'=>$response],
            ]);
        }

        $user = User::where('email', $reset->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();



        $userIpInfo = getIpInfo();
        $userBrowser = osBrowser();
        notify($user, 'PASS_RESET_DONE', [
            'operating_system' => @$userBrowser['os_platform'],
            'browser' => @$userBrowser['browser'],
            'ip' => @$userIpInfo['ip'],
            'time' => @$userIpInfo['time']
        ],['email']);


        $response[] = 'Password changed successfully';
        return response()->json([
            'remark'=>'password_changed',
            'status'=>'success',
            'message'=>['success'=>$response],
        ]);
    }

    protected function rules()
    {
        $passwordValidation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required','confirmed',$passwordValidation],
        ];
    }

    
}
