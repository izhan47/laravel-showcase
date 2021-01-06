<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PetProGallery extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'image_full_path', 'image_thumb_full_path', 'image_small_thumb_full_path'];

    public function petPro()
    {
        return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
    }

    //image_full_path
    public function getImageFullPathAttribute()
    {
        if (!empty($this->gallery_image)) {
            return Storage::url(config("wagenabled.path.url.pet_pro_gallery_image_path") . $this->gallery_image);
        }    
        return asset('admin-theme/images/default-wag.svg');      
    }

    //image_thumb_full_path
    public function getImageThumbFullPathAttribute()
    {
        if (!empty($this->gallery_image)) {
            $image_url = Storage::url(config("wagenabled.path.url.pet_pro_gallery_image_path").'thumb/'.  $this->gallery_image);

            if($this->is_cropped_image) {
                return $image_url;
            }
            return Storage::url(config("wagenabled.path.url.pet_pro_gallery_image_path"). $this->gallery_image);
        }    
        return asset('admin-theme/images/default-wag.svg');      
    }

    //image_small_thumb_full_path
    public function getImageSmallThumbFullPathAttribute()
    {
        if (!empty($this->gallery_image)) {
            $image_url = Storage::url(config("wagenabled.path.url.pet_pro_gallery_image_path").'small-thumb/'.  $this->gallery_image);
                return $image_url;
        }    
        return asset('admin-theme/images/default-wag.svg');      
    }

}
