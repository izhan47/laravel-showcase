<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\HubspotHelpers;
use App\Http\WagEnabledHelpers;
use App\Models\City;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class NewsletterController extends Controller
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
            'unique' => 'Your email address already registered'
        ];
           
        $validator = Validator::make($request->all(), [              
            'email' => 'required|email|unique:newsletters',
            'first_name' => 'required | max: 255', 
            'zipcode' => ' max: 20',           
        ], $customMessages);
        
        
        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['email', 'first_name', 'zipcode']);

        $city = City::where('zipcode', $input["zipcode"])->first();        
        if ( !$city ) { 
            $validator->getMessageBag()->add('zipcode', 'Please enter correct zipcode'); 
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $isSaved = Newsletter::create($input);
        
        if ($isSaved) {

            $arr = array(
               'fields' => array(
                    array(
                        'name' => 'email',
                        'value' => $isSaved->email
                    ),
                    array(
                        'name' => 'firstname',
                        'value' => $isSaved->first_name
                    ),
                    array(
                        'name' => 'zip',
                        'value' => $isSaved->zipcode
                    )
                ),
               'context' => array(              
                    "pageUri" => env('REACT_SERVER_BASE_URL'),
                    "pageName" => "Newsletter page"                
                ),
            );

            $post_json = json_encode($arr);
            HubspotHelpers::storeForm($post_json, env('HUBSPOT_PORTAL_ID'), env('HUBSPOT_NEWSLETTER_FORM_GUID'));

            $this->message = " Thank you for subscribing to Wag Enabled!"; 
            $this->code = $this->statusCodes['success'];   
        }
       
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

}
