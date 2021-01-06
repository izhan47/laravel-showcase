<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BusinessRequest extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'formated_created_at'];

   
    //formated_created_at
    public function getFormatedCreatedAtAttribute()
    {
        $data = "";
        if(!empty($this->created_at)) {
            $data = Carbon::parse($this->created_at)->format('m/d/y');
        }
        return $data;
    }
}
