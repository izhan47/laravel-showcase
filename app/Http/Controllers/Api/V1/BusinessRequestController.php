<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Mail\SendBusinessRequestMail;
use App\Models\BusinessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Validator;

class BusinessRequestController extends Controller
{          
    public function __construct()
    {   
        $this->statusCodes = config("wagenabled.status_codes");
        $this->responseData = [];
        $this->message = "Please, try again!";
        $this->code = config("wagenabled.status_codes.normal_error");
    }

    public function store(Request $request) 
    {     
        $customMessages = [
            'unique' => 'Business request already sent using this email address.'
        ];
           
        $validator = Validator::make($request->all(), [
            'first_name' => 'required | max: 255',       
            'last_name' => 'required | max: 255',       
            'business_name' => 'required | max: 255',                 
            'contact_email' => 'required|email|unique:business_requests',       
            'message' => 'required',       
        ], $customMessages);
        
        
        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['first_name', 'last_name', 'business_name', 'contact_email', 'message']);
        $isSaved = BusinessRequest::create($input);
        
        if ($isSaved) {
            Mail::to(config('wagenabled.send_contact_to_email'))->send(new SendBusinessRequestMail($isSaved));
            $this->message = "Your business request has been received, We'll get back to you shortly."; 
            $this->code = $this->statusCodes['success']; 
        }
       
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

}
