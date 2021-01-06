<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSavedVideo extends Pivot
{
	protected $table = "user_saved_videos";
	
	protected $guarded = ['id', 'created_at', 'updated_at']; 

    public function watchAndLearn()
    {
        return $this->belongsTo('App\Models\WatchAndLearn', 'watch_and_learn_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Users', 'user_id', 'id');
    }
}
