<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\HubspotHelpers;
use App\Http\WagEnabledHelpers;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class SocialSignUpController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {   
        $this->statusCodes = config("wagenabled.status_codes");
    }

    public function signup(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',           
            'email' => 'required|email',            
            'provider' => 'required|in:facebook,google,normal',
            'provider_id' => 'required',
            'latitude' => 'nullable',       
            'longitude' => 'nullable',   
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }  

        $responseData =[];
        $login_email = $request->get("email", "");
        $input = $request->only(['provider', 'provider_id', 'name', 'latitude', 'longitude']);        
        $user = "";
        $LoginUserAlreadyExists = User::where("email", $login_email )->withTrashed()->first();        
      
            if( $LoginUserAlreadyExists ) {
                return WagEnabledHelpers::apiJsonResponse($responseData, config('wagenabled.status_codes.auth_fail'), "Already resisted with same email please login.");               
            } 
            else {
                $input["email"] = $login_email;   
                $user = User::create($input);

                // entry 
                $arr = array(
                   'fields' => array(
                        array(
                           'name' => 'email',
                           'value' => $user->email
                        ),
                        array(
                           'name' => 'name',
                           'value' => $user->name
                        )
                    ),
                   'context' => array(              
                        "pageUri" => env('REACT_SERVER_BASE_URL').'/signup',
                        "pageName" => "Signup page"                
                    ),
                );          
                $post_json = json_encode($arr);
                HubspotHelpers::storeForm($post_json, env('HUBSPOT_PORTAL_ID'), env('HUBSPOT_SIGNUP_FORM_GUID'));
            }                    
            
            $token = JWTAuth::fromUser($user);

            return response()->json([
                            'token' => "Bearer " . $token,
                            'user' => $user,
                        ]);     
    }
}
