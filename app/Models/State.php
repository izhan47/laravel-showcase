<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    protected $primaryKey = 'id';
    
    protected $guarded = ['id'];
    protected $hidden = [];
    protected $appends = [];

    public function users()
    {
        return $this->hasMany('User');
    }

    public function countries()
    {
        return $this->hasMany('App\Models\City', 'state_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }
    
    public function petpros()
    {
           //return $this->belongsToMany(RelatedModel, pivot_table_name, foreign_key_of_current_model_in_pivot_table, foreign_key_of_other_model_in_pivot_table);
        return $this->belongsToMany(
            'App\Models\PetPro',
            'pet_country_state_city',
            'state_id',
            'pet_pro_id');
    }
}
