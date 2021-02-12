<?php

namespace App\Models;

use App\Http\WagEnabledHelpers;
use App\Observers\WatchAndLearnObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class WatchAndLearn extends Model
{
    use SoftDeletes;
    
	protected $table = "watch_and_learn";

	protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'formated_created_at', 'thumbnail_full_path', 'thumbnail_thumb_full_path', 'video_full_path', 'formated_duration'];

    public static function boot()
    {
        parent::boot();
        static::observe(WatchAndLearnObserver::class);

        static::deleted(function ($model) {
            $deleteFileList = array();
            if (isset($model->thumbnail) && $model->thumbnail) {                
                $deleteFileList[] =  config("wagenabled.path.doc.watch_and_learn_thumbnail_path").$model->thumbnail;
                $deleteFileList[] =  config("wagenabled.path.doc.watch_and_learn_thumbnail_path").'thumb/'.$model->thumbnail;
                WagEnabledHelpers::deleteIfFileExist($deleteFileList);           
            }            
            if (isset($model->video_type) && $model->video_type == 'video_upload' ) {
                $deleteFileList[] =  config("wagenabled.path.doc.watch_and_learn_video_path").$model->video_file;
            }            
            WagEnabledHelpers::deleteIfFileExist($deleteFileList);           
        });
    }

    //formated_duration
    public function getFormatedDurationAttribute()
    {
        $data = "";
        if(!empty($this->duration)) {
            $data = Carbon::parse($this->duration)->format('H:i:s');
        }
        return $data;
    }

    //formated_created_at
    public function getFormatedCreatedAtAttribute()
    {
        $data = "";
        if(!empty($this->created_at)) {
            $data = Carbon::parse($this->created_at)->format('F d, Y');
        }
        return $data;
    }


    //thumbnail_full_path
    public function getThumbnailFullPathAttribute()
    {
        if (!empty($this->thumbnail)) {
            return Storage::url(config("wagenabled.path.url.watch_and_learn_thumbnail_path") . $this->thumbnail);
        }    
        return asset('admin-theme/images/default.png');      
    }

    //thumbnail_thumb_full_path
    public function getThumbnailThumbFullPathAttribute()
    {
        if (!empty($this->thumbnail)) {
            return Storage::url(config("wagenabled.path.url.watch_and_learn_thumbnail_path").'thumb/'. $this->thumbnail);
        }    
        return asset('admin-theme/images/default.png');      
    }

    //video_full_path
    public function getVideoFullPathAttribute()
    {   
        if( $this->video_type == 'video_upload' ) {            
            if (!empty($this->video_file)) {
                return Storage::url(config("wagenabled.path.url.watch_and_learn_video_path"). $this->video_file);
            }    
        }
        else {
           return $this->embed_link;
        }
        return '';      
    }
 
    public function category()
    {
        return $this->belongsTo('App\Models\WatchAndLearnCategory', 'category_id', 'id')->withTrashed();
    }

    public function categories()
    {
        return $this->hasMany('App\Models\WatchAndLearnSelectedCategory','watch_and_learn_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo('App\Models\WatchAndLearnAuthor', 'author_id', 'id')->withTrashed();
    }

    public function comments()
    {
        return $this->hasMany('App\Models\WatchAndLearnComment', 'watch_and_learn_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_saved_videos', 'watch_and_learn_id', 'user_id')->using('App\Models\UserSavedVideo')->withTrashed(); 
    }

    public function deals()
    {
        return $this->hasMany('App\Models\WatchAndLearnDeal', 'watch_and_learn_id', 'id')->orderBy('id', 'desc');
    }

    public function scopeProductReviewCategory($query)
    {
        $parent_product_review_id = config("wagenabled.product_review_category_id");
        
        if( $parent_product_review_id ) {           
            return $query->whereHas('category', function($q) use($parent_product_review_id){
                $q->where('watch_and_learn_categories.parent_id', $parent_product_review_id);
            });
        } 

        return $query;
    }   

    public function scopeGetWatchAndLearnCategory($query)
    {
        $parent_product_review_id = config("wagenabled.product_review_category_id");
        $query = $query->whereHas('category', function($q) use($parent_product_review_id){
            if( $parent_product_review_id ) {           
                $q->where('watch_and_learn_categories.id', '!=', $parent_product_review_id);
            } 
            $q->where('watch_and_learn_categories.parent_id', 0);
        });

        return $query;       
    }  
}
