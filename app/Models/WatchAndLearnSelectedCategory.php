<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
class WatchAndLearnSelectedCategory extends Model
{
    protected $guarded = ['id'];


	public function watchAndLearn()
	{
		return $this->belongsTo('App\Models\WatchAndLearn', 'watch_and_learn_id', 'id');
	}

	public function category()
	{
		return $this->belongsTo('App\Models\WatchAndLearnCategory', 'selected_category_id', 'id');
	}
}
