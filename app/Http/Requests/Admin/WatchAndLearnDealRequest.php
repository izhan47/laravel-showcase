<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WatchAndLearnDealRequest extends FormRequest
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
            'deal' => 'required | max:255',                    
            'fine_print' => 'required',                    
            'start_date' => 'nullable | date',                    
            'end_date' => 'nullable | date | after_or_equal:start_date'                                   
        ];
        
        return $result;

    }
}
