<?php

namespace App\Models;

use App\Http\WagEnabledHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class WatchAndLearnAuthor extends Model
{
    use SoftDeletes;

    protected $table = "watch_and_learn_authors";
    
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'image_full_path', 'image_thumb_full_path', 'formated_created_at'];

    public static function boot()
    {
        parent::boot();
        static::deleted(function ($model) {
            /*
            if (isset($model->profile_image) && $model->profile_image) {                
                $deleteFileList = array();
                $deleteFileList[] =  config("wagenabled.path.doc.watch_and_learn_author_path").$model->profile_image;
                $deleteFileList[] =  config("wagenabled.path.doc.watch_and_learn_author_path").'thumb/'.$model->profile_image;
                WagEnabledHelpers::deleteIfFileExist($deleteFileList);           
            }
            */
        });
    }

    public function watchAndLearns()
    {
        return $this->hasMany('App\Models\WatchAndLearn', 'author_id', 'id');
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

    //image_full_path
    public function getImageFullPathAttribute()
    {
        if (!empty($this->profile_image)) {
            return Storage::url(config("wagenabled.path.url.watch_and_learn_author_path") . $this->profile_image);
        }    
        return asset('admin-theme/images/default_user.svg');      
    }

    //image_thumb_full_path
    public function getImageThumbFullPathAttribute()
    {
        if (!empty($this->profile_image)) {
            return Storage::url(config("wagenabled.path.url.watch_and_learn_author_path").'thumb/'. $this->profile_image);
        }    
        return asset('admin-theme/images/default_user.svg');      
    }

}
