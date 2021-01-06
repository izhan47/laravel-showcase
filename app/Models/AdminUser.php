<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $guarded = ['id'];
    protected $appends = [ 'formated_created_at'];

    //Append Values -------------------------------------------------

    //formated_created_at
    public function getFormatedCreatedAtAttribute()
    {
        $data = "";
        if(!empty($this->created_at)) {
            $data = Carbon::parse($this->created_at)->format('m/d/Y');
        }
        return $data;
    }
}
