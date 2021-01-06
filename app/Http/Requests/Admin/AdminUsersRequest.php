<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUsersRequest extends FormRequest
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
            'email' => [
                "required",
                "email",
                Rule::unique('admin_users')->ignore($this->admin_user),
            ],                
            'password' => 'required|min:6',   
            /*'phone_number' => 'required',*/ 
        ];

        switch($this->method())
        {
            case 'PUT':
            {
                $result['password'] = '';                
            }
            default:break;
        }
        return $result;

    }
}
