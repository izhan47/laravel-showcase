<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserLovedPetPro extends Pivot
{
	protected $table = 'user_loved_pet_pros';
	
   	protected $guarded = ['id', 'created_at', 'updated_at'];  

   	public function petPro()
   	{
   	    return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
   	}

}
