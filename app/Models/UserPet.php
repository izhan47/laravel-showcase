<?php

namespace App\Models;

use App\Http\WagEnabledHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserPet extends Model
{
	protected $table = "user_pets";

	protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'pet_image_full_path', 'pet_image_thumb_full_path'];

    public static function boot()
    {
        parent::boot();
        static::deleted(function ($model) {
            if (isset($model->pet_image) && $model->pet_image) {                
                $deleteFileList = array();
                $deleteFileList[] =  config("wagenabled.path.doc.users_pet_image_path").$model->pet_image;
                $deleteFileList[] =  config("wagenabled.path.doc.users_pet_image_path").'thumb/'.$model->pet_image;
                WagEnabledHelpers::deleteIfFileExist($deleteFileList);           
            }
        });
    }

    //pet_image_full_path
    public function getPetImageFullPathAttribute()
    {
        if (!empty($this->pet_image)) {
            return Storage::url(config("wagenabled.path.url.users_pet_image_path") . $this->pet_image);
        }    
        return asset('admin-theme/images/default.png');       
    }

    //pet_image_thumb_full_path
    public function getPetImageThumbFullPathAttribute()
    {
        if (!empty($this->pet_image)) {
            return Storage::url(config("wagenabled.path.url.users_pet_image_path") .'thumb/'. $this->pet_image);
        }    
        return asset('admin-theme/images/default.png');       
    }

    public function breed()
    {
        return $this->belongsToMany('App\Models\Breed', 'users_pet_breeds', 'users_pet_id', 'breed_id')->using('App\Models\UsersPetBreed'); 
    }
}
