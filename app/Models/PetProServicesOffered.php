<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PetProServicesOffered extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $table = 'pet_pro_services_offered';

	public function petPro()
    {
        return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
    }
}
