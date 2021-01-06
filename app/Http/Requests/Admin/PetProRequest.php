<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PetProRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
         $result = [
            'store_name' => 'required | max:255',                    
            'email' => 'nullable | email',                    
            'website_url' => 'nullable | url',                    
            'phone_number' => 'nullable | max:15',                    
            'address_line_1' => 'nullable | max:255',                    
            'address_line_2' => 'nullable | max:255',                    
            'category_id' => 'required',                    
            'state_id' => 'nullable | integer',                    
            'city_id' => 'nullable | integer',                    
            'postal_code' => 'nullable | max:255',                    
            'description' => 'nullable',      
            'donation_link' => 'nullable | url',                                          
        ];
      
        return $result;

    }
}
