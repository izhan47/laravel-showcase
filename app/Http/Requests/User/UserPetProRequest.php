<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserPetProRequest extends FormRequest
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
            'email' => 'required | email',                    
            'website_url' => 'nullable | url',                    
            'phone_number' => 'required | max:15',                    
            'address_line_1' => 'required | max:255',                    
            'address_line_2' => 'nullable | max:255',                    
            'category_id' => 'nullable',                                        
            'postal_code' => 'nullable | max:255',                    
            'description' => 'required',      
            'donation_link' => 'nullable | url',                                          
        ];
        return $result;

    }
}
