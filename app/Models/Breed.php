<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
	protected $table = "breeds";

	protected $guarded = ['id', 'created_at', 'updated_at'];
  
    public function dogs()
    {
        return $this->hasMany('App\Models\UserPet', 'breed_id', 'id');
    }
}
