<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestimonialRequest extends FormRequest
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
            'title' => 'required',
            'description' => 'required',
			'client_name' => 'required',
			'client_title' => 'required',
			'image' => 'required | mimes:jpeg,jpg,png',
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
