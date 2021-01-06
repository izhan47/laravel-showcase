<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $primaryKey = 'id';
    
    protected $guarded = ['id'];
    protected $hidden = [];
    protected $appends = [];

    public function users()
    {
        return $this->hasMany('User');
    }

   	public function state()
   	{
   	    return $this->belongsTo('App\Models\State', 'state_id', 'id');
   	} 
}
