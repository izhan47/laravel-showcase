<?php

namespace App\Models;

use App\Http\WagEnabledHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
	protected $guarded = ['id'];

    protected $appends = [ 'formated_created_at', 'testimonial_image_full_path', 'testimonial_image_thumb_full_path'];

	public static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $deleteFileList = array();
            if (isset($model->image) && $model->image) {                
                $deleteFileList[] =  config("wagenabled.path.doc.testimonial_image_path").$model->image;
                $deleteFileList[] =  config("wagenabled.path.doc.testimonial_image_path").'thumb/'.$model->image;
                WagEnabledHelpers::deleteIfFileExist($deleteFileList);           
            }           
        });
	}
	
	public function getFormatedCreatedAtAttribute()
    {
        $data = "";
        if(!empty($this->created_at)) {
            $data = Carbon::parse($this->created_at)->format('F d, Y');
        }
        return $data;
    }
	
	public function getTestimonialImageFullPathAttribute()
    {
        if (!empty($this->image)) {
            return Storage::url(config("wagenabled.path.url.testimonial_image_path") . $this->image);
        }    
        return '';      
    }

    public function getTestimonialImageThumbFullPathAttribute()
    {
        if (!empty($this->image)) {
            return Storage::url(config("wagenabled.path.url.testimonial_image_path").'thumb/'. $this->image);
        }    
        return '';      
    }
}
