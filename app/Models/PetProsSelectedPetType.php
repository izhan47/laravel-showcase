<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetProsSelectedPetType extends Model
{
    protected $guarded = ['id'];

    public function petPro()
    {
        return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
    }

    public function businessNature()
    {
        return $this->belongsTo('App\Models\PetType', 'pet_type_id', 'id');
    }
}
