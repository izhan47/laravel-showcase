<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetProTimetable extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at']; 

    public function watchAndLearn()
    {
        return $this->belongsTo('App\Models\WatchAndLearn', 'watch_and_learn_id', 'id');
    }
}
