<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
class PetProSelectedBusinessNature extends Model
{
    protected $guarded = ['id'];
  
	public function petPro()
	{
		return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
	}

	public function businessNature()
	{
		return $this->belongsTo('App\Models\BusinessNature', 'business_id', 'id');
	}
}
