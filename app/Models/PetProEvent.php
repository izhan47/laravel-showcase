<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetProEvent extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'formated_event_date', 'formated_event_month', 'formated_event_start_time', 'formated_event_end_time', 'formated_event_date_day', 'formated_event_start_date', 'formated_event_end_date'];

    public function petPro()
    {
        return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
    }

    //formated_event_date
    public function getFormatedEventDateAttribute()
    {
        $data = "";
        if(!empty($this->event_date)) {
            $data = Carbon::parse($this->event_date)->format('m/d/y');
        }
        return $data;
    } 

    //formated_event_date_day
    public function getFormatedEventDateDayAttribute()
    {
        $data = "";
        if(!empty($this->event_date)) {
            $data = Carbon::parse($this->event_date)->format('d');
        }
        return $data;
    }  

    //formated_event_start_date
    public function getFormatedEventStartDateAttribute()
    {
        $data = "";
        if(!empty($this->event_date)) {
            $data = Carbon::parse($this->event_date)->format('M, d Y');
        }
        return $data;
    } 

    //formated_event_end_date
    public function getFormatedEventEndDateAttribute()
    {
        $data = "";
        if(!empty($this->event_date)) {
            $data = Carbon::parse($this->event_end_date)->format('M, d Y');
        }
        return $data;
    } 

    //formated_event_month
    public function getFormatedEventMonthAttribute()
    {
        $data = "";
        if(!empty($this->event_date)) {
            $data = Carbon::parse($this->event_date)->format('F');
        }
        return $data;
    }

    //formated_event_start_time
    public function getFormatedEventStartTimeAttribute()
    {
        $data = "";
        if(!empty($this->start_time)) {
            $data = Carbon::parse($this->start_time)->format('h:i a');
        }
        return $data;
    } 
    
    //formated_event_end_time
    public function getFormatedEventEndTimeAttribute()
    {
        $data = "";
        if(!empty($this->end_time)) {
            $data = Carbon::parse($this->end_time)->format('h:i a');
        }
        return $data;
    } 

    public function scopeActive($query)
    {
        $current_date_time_ob = Carbon::now('UTC');
        $current_date = $current_date_time_ob->format('Y-m-d');
        $current_time = $current_date_time_ob->format('H:i:s');

        return $query->where(function($q) use($current_date, $current_time) {
                $q->where(function($q){
                    $q->whereNull('event_date');
                })
                ->orWhere(function($q) use($current_date, $current_time) {
                    $q->where('event_end_date', '>=', $current_date)
                      /*->where('start_time', '>=', $current_time)*/;
                });
            })->where('status', 'active');
    }   
}
