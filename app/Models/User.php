<?php

namespace App\Models;

use App\Http\WagEnabledHelpers;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
     use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [ 'formated_created_at', 'profile_image_full_path', 'profile_image_thumb_full_path'];

    public static function boot()
    {
        parent::boot();
        static::deleted(function ($model) {
            if (isset($model->profile_image) && $model->profile_image) {                
                $deleteFileList = array();
                $deleteFileList[] =  config("wagenabled.path.doc.user_profile_image_path").$model->profile_image;
                $deleteFileList[] =  config("wagenabled.path.doc.user_profile_image_path").'thumb/'.$model->profile_image;
                WagEnabledHelpers::deleteIfFileExist($deleteFileList);           
            }
        });
    }

    // jwt 
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    //formated_created_at
    public function getFormatedCreatedAtAttribute()
    {
        $data = "";
        if(!empty($this->created_at)) {
            $data = Carbon::parse($this->created_at)->format('m/d/y');
        }
        return $data;
    }

    //profile_image_full_path
    public function getProfileImageFullPathAttribute()
    {
        if (!empty($this->profile_image)) {
            return Storage::url(config("wagenabled.path.url.user_profile_image_path") . $this->profile_image);
        }    
        return asset('admin-theme/images/default_user.svg');       
    }

    //profile_image_thumb_full_path
    public function getProfileImageThumbFullPathAttribute()
    {
        if (!empty($this->profile_image)) {
            return Storage::url(config("wagenabled.path.url.user_profile_image_path").'thumb/'. $this->profile_image);
        }    
        return asset('admin-theme/images/default_user.svg');       
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function pets()
    {
        return $this->hasMany('App\Models\UserPet', 'user_id', 'id');
    }
    
    public function savedVideos()
    {
        return $this->belongsToMany('App\Models\WatchAndLearn', 'user_saved_videos', 'user_id', 'watch_and_learn_id')->using('App\Models\UserSavedVideo'); 
    }

    public function lovedPetPro()
    {
        return $this->belongsToMany('App\Models\PetPro', 'user_loved_pet_pros', 'user_id', 'pet_pro_id')->using('App\Models\UserLovedPetPro'); 
    }

    public function petProReviews()
    {
        return $this->hasMany('App\Models\PetProReview', 'user_id', 'id');
    }
}
