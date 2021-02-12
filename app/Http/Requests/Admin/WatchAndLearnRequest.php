<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WatchAndLearnRequest extends FormRequest
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
            'category_id' => 'required',                    
            'title' => 'required | max:255',                    
            'author_id' => 'nullable | integer',                    
            'description' => 'nullable',                    
            'image' => 'required | mimes:jpeg,jpg,png',                    
            'blog_meta_description' => 'required | max:255',                    
            'alt_image_text' => 'required | max:255',                    
            // 'video_type' => 'required',                    
            // 'video_file' => 'nullable | mimes:mp4',                    
            // 'embed_link' => 'nullable | regex: /^https:\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]+/',                    
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
