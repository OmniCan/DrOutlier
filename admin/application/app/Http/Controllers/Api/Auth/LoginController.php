<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $email;

    public function __construct()
    {
        $this->email = $this->determineLoginField();
    }

   
    public function login(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            $this->email => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        
        $credentials = [$this->email => $request->input('email'), 'password' => $request->input('password')];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'remark' => 'error',
                'status' => 'unsuccessfull',
                'message' => ['error' => ['Email or password  is invalid']],
            ]);
        }

     
        $user = $request->user();
        $tokenResult = $user->createToken('auth_token')->plainTextToken;

       
        $this->logUserLogin($request, $user);

       
        return response()->json([
            'remark' => 'login_success',
            'status' => 'success',
            'message' => ['success' => ['Login Successful']],
            'data' => [
                'user' => $user,
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ],
        ]);
    }

   
    private function determineLoginField()
    {
        $loginInput = request()->input('email');
        return filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }

   
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'remark' => 'logout_success',
            'status' => 'success',
            'message' => ['success' => ['Logout Successful']],
        ]);
    }

    
    protected function logUserLogin(Request $request, $user)
    {
        $ip = $this->getRealIP();
        $userAgent = $this->getUserAgent();
        $userLogin = new UserLogin();

        $existingLogin = UserLogin::where('user_ip', $ip)->first();

        if ($existingLogin) {
            $userLogin->longitude = $existingLogin->longitude;
            $userLogin->latitude = $existingLogin->latitude;
            $userLogin->city = $existingLogin->city;
            $userLogin->country_code = $existingLogin->country_code;
            $userLogin->country = $existingLogin->country;
        } else {
            $ipInfo = $this->getIpInfo();
            $userLogin->longitude = $ipInfo['longitude'] ?? null;
            $userLogin->latitude = $ipInfo['latitude'] ?? null;
            $userLogin->city = $ipInfo['city'] ?? null;
            $userLogin->country_code = $ipInfo['country_code'] ?? null;
            $userLogin->country = $ipInfo['country'] ?? null;
        }

        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = $userAgent['browser'] ?? null;
        $userLogin->os = $userAgent['os'] ?? null;
        $userLogin->save();
    }

   
    private function getRealIP()
    {
        return request()->ip(); 
    }

    
    private function getUserAgent()
    {
        
        return [
            'browser' => request()->header('User-Agent'),
            'os' => 'Unknown OS',
        ];
    }

   
    private function getIpInfo()
    {
        
        return [
            'longitude' => '0.000',
            'latitude' => '0.000',
            'city' => 'Unknown City',
            'country_code' => 'XX',
            'country' => 'Unknown Country',
        ];
    }
}
