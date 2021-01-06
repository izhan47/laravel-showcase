<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UsersPetBreed extends Pivot
{
	protected $table = "users_pet_breeds";
	
	protected $guarded = ['id', 'created_at', 'updated_at']; 

    public function breed()
    {
        return $this->belongsTo('App\Models\Breed', 'breed_id', 'id');
    }

    public function pet()
    {
        return $this->belongsTo('App\Models\UserPet', 'user_id', 'id');
    }
}
