<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WatchAndLearnAuthorRequest extends FormRequest
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
            'website_link' => 'nullable | url',                 
            'image' => 'required | mimes:jpeg,jpg,png',                    
            'about' => 'required'                                                
        ];
        switch($this->method())
        {
            case 'PUT':
            {
                $result['image'] = '';                
            }
            default:break;
        }
        return $result;

    }
}
