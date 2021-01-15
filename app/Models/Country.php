<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'id';
    
    protected $guarded = ['id'];
    protected $hidden = [];
    protected $appends = [];

    public function users()
    {
        return $this->hasMany('User');
    }

    public function states()
    {
        return $this->hasMany('App\Models\State', 'country_id', 'id');
    }

    public function petpros()
    {
        //return $this->belongsToMany(RelatedModel, pivot_table_name, foreign_key_of_current_model_in_pivot_table, foreign_key_of_other_model_in_pivot_table);
        return $this->belongsToMany(
                'App\Models\PetPro',
                'pet_country_state_city',
                'country_id',
                'pet_pro_id');
    }
    
}

