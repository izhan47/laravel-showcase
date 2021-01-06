<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Models\Contact;
use App\Models\PetPro;
use App\Models\PetProDeal;
use App\Models\WatchAndLearn;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Validator;

class HomeController extends Controller
{          
    public function __construct()
    {   
        $this->statusCodes = config("wagenabled.status_codes");
        $this->responseData = [];
        $this->message = "Please, try again!";
        $this->code = config("wagenabled.status_codes.normal_error");
    }

    public function getTestimonialCounts(Request $request) 
    {                             

        $this->responseData["watchAndLearnCounts"] = WatchAndLearn::count();
        $this->responseData["petProCounts"] = PetPro::count();
        $this->responseData["dealCounts"] = PetProDeal::active()->count();
        $this->message = ""; 
        $this->code = $this->statusCodes['success']; 

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getFeaturedPetProList(Request $request) 
    {            
        $this->responseData["featured_pet_pro_list"] = PetPro::select(['id', 'slug', 'featured_title', 'store_name', 'featured_description', 'is_featured_pet_pro' ])
                                                            ->with('coverImage')               
                                                            ->orderBy('id', 'desc')                   
                                                            ->where('is_featured_pet_pro', 1)
                                                            ->get();
        $this->message = "";
        $this->code = $this->statusCodes['success']; 
       
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
	}
	
	public function getTestimonialList(Request $request) 
    {            
        $this->responseData["testimonial_list"] = Testimonial::all();
        $this->message = "";
        $this->code = $this->statusCodes['success']; 
       
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

}
