<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WatchAndLearnMedias extends Model
{
	protected $table = "watch_and_learn_medias";
	protected $guarded = ['id', 'created_at', 'updated_at'];
}
