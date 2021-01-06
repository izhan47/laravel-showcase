<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WatchAndLearnComment extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at']; 

	protected $appends = [ 'formated_created_at'];

    protected $table = "watch_and_learn_comments";

    public function watchAndLearn()
    {
        return $this->belongsTo('App\Models\WatchAndLearn', 'watch_and_learn_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function parent()
    {
       return $this->belongsTo('App\Models\WatchAndLearnComment', 'parent_comment_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\WatchAndLearnComment', 'parent_comment_id');
    }

    public function allChildren()
    {
        return $this->hasMany('App\Models\WatchAndLearnComment', 'parent_comment_id')->with('allChildren');
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
}
