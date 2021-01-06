<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\HubspotHelpers;
use App\Http\WagEnabledHelpers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class RegisterController extends Controller
{
    
    protected function create(Request $request)
    {       
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',            
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'latitude' => 'nullable',       
            'longitude' => 'nullable',    
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['name', 'email', 'password', 'latitude', 'longitude']);        
        $input['password'] =  Hash::make($input['password']); 
        $user = User::create($input);        
        $token = JWTAuth::fromUser($user);     
        $userResponse = [];

        if($user) {
            $userResponse = $user;   
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

        $data = array(
            'token' => "Bearer " . $token,
            'user' => $userResponse,
        );
        
        $message = "User registered successfully";
        return WagEnabledHelpers::apiJsonResponse($data, "", $message);        
    }
}
