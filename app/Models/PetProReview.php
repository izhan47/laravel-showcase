<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PetProReview extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at']; 

	protected $appends = [ 'formated_created_at'];


	//formated_created_at
	public function getFormatedCreatedAtAttribute()
	{
	    $data = "";
	    if(!empty($this->created_at)) {
	        $data = Carbon::parse($this->created_at)->format('d F, Y');
	    }
	    return $data;
	}

    public function petPro()
    {
        return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
