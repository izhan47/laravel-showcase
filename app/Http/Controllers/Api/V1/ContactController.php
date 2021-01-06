<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Mail\SendContactMail;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class ContactController extends Controller
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
        $validator = Validator::make($request->all(), [
            'name' => 'required | max: 255',                                    
            'email' => 'required | email',       
            'message' => 'required',       
        ]);        
        
        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['name', 'email', 'message']);
        $isSaved = Contact::create($input);
        
        if ($isSaved) {

            // send email to Wag Enabled 
            Mail::to(config('wagenabled.send_contact_to_email'))->send(new SendContactMail($isSaved));

            $this->message = "Your message has been received, We'll get back to you shortly."; 
            $this->code = $this->statusCodes['success'];   
        }
       
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

}
