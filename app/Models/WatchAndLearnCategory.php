<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WatchAndLearnCategory extends Model
{
    use SoftDeletes;
    
	protected $table = "watch_and_learn_categories";

	protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'formated_created_at'];

    //formated_created_at
    public function getFormatedCreatedAtAttribute()
    {
        $data = "";
        if(!empty($this->created_at)) {
            $data = Carbon::parse($this->created_at)->format('m/d/y');
        }
        return $data;
    }
  
    public function watchAndLearn()
    {
        return $this->hasMany('App\Models\WatchAndLearn', 'category_id', 'id');
    }
    public function watchAndLearns()
    {
        return $this->hasMany('App\Models\WatchAndLearnSelectedCategory', 'selected_category_id', 'id');
    }

    public function scopeProductReviewCategory($query)
    {
        $parent_product_review_id = config("wagenabled.product_review_category_id");
        
        if( $parent_product_review_id ) {            
            return $query->where('watch_and_learn_categories.parent_id', $parent_product_review_id);
        } 

        return $query;
    }   

    public function scopeGetWatchAndLearnCategory($query)
    {
        $parent_product_review_id = config("wagenabled.product_review_category_id");    
        if( $parent_product_review_id ) {            
            $query = $query->where('watch_and_learn_categories.id', '!=', $parent_product_review_id);
        } 
        return $query->where('watch_and_learn_categories.parent_id', 0);
    }   
}
