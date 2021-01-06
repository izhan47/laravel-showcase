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
}

