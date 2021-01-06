<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PetProSelectedCategory extends Model
{   
	protected $guarded = ['id'];
  
	public function petPro()
	{
		return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
	}

	public function category()
	{
		return $this->belongsTo('App\Models\PetProCategory', 'category_id', 'id');
	}
}
