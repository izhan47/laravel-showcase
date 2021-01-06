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
}
