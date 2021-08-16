<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class LoginController extends Controller
{    
    use AuthenticatesUsers;
   
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);    

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);
        $user = User::where('email', $credentials['email'])->withTrashed()->first();    

        if ( !$user ) { 
            $validator->getMessageBag()->add('email', 'Please enter correct email address.'); 
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        } elseif( ! Hash::check($credentials['password'], $user->password)) {            
            $validator->getMessageBag()->add('password', 'Please enter correct password.');          
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        if( $user->deleted_at == null  ) {           
            if ($token = JWTAuth::attempt($credentials)) {                        
                $user = auth()->user();
                $userResponse = [];
                if($user) {                    
                    $userResponse = $user;                   
                }                            
                return response()->json([
                            'token' => "Bearer " . $token,
                            'user' => $userResponse,
                        ]);
            } 
        }
        else {
            $validator->getMessageBag()->add('email', 'Please contact Wag Enabled support'); 
            return WagEnabledHelpers::apiValidationFailResponse($validator);           
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);        
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
