<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PetProEventRequest extends FormRequest
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
            'name' => 'required | max:255',    
            'description' => 'nullable',    
            'event_date' => 'nullable | date',                    
            'start_time' => 'nullable',                                   
            'end_time' => 'nullable',                                   
            'address' => 'nullable',                                              
            'url' => 'required | url',                                              
        ];
        
        return $result;

    }
}
