<?php

namespace App\Models;

use App\Http\WagEnabledHelpers;
use App\Models\PetProTimetable;
use App\Observers\PetProObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PetPro extends Model
{   
    use SoftDeletes;
    
	protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'formated_created_at', "formatted_timetable", 'is_timetable_entered'];

    public static function boot()
    {
        parent::boot();
        static::observe(PetProObserver::class);       
    }

    //formated_created_at
    public function getFormatedCreatedAtAttribute()
    {
        $data = "";
        if(!empty($this->created_at)) {
            $data = Carbon::parse($this->created_at)->format('m/d/y');
        }
        return $data;
    }
	
	public function categories()
    {
        return $this->hasMany('App\Models\PetProSelectedCategory','pet_pro_id', 'id');
    }

    public function servicesOffered()
    {
        return $this->hasMany('App\Models\PetProServicesOffered','pet_pro_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }
    
    public function countries()
    {
        return $this->belongsToMany(
            'App\Models\Country',
            'pet_country_state_city',
            'pet_pro_id',
            'country_id');
    }

    public function states()
    {
        return $this->belongsToMany(
            'App\Models\State',
            'pet_country_state_city',
            'pet_pro_id',
            'state_id');
    }

    public function cities()
    {
        return $this->belongsToMany(
            'App\Models\City',
            'pet_country_state_city',
            'pet_pro_id',
            'city_id');
    }
    

    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function timetable()
    {
        return $this->hasMany('App\Models\PetProTimetable', 'pet_pro_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\PetProReview', 'pet_pro_id', 'id');
    }

    public function coverImage()
    {
        return $this->hasOne('App\Models\PetProGallery', 'pet_pro_id', 'id')->Where('is_cover_image', '1');
    }

    public function otherImage()
    {
        return $this->hasMany('App\Models\PetProGallery', 'pet_pro_id', 'id')->Where('is_cover_image', '0');
    }

    public function images()
    {
        return $this->hasMany('App\Models\PetProGallery', 'pet_pro_id', 'id')->orderBy('is_cover_image', 'desc')->orderBy('id', 'asc');
    }

    public function deals()
    {
        return $this->hasMany('App\Models\PetProDeal', 'pet_pro_id', 'id')->orderBy('id', 'desc');
    }

    public function events()
    {
        return $this->hasMany('App\Models\PetProEvent', 'pet_pro_id', 'id')->orderBy('event_date', 'asc');
    }

    public function getFormattedTimetableAttribute()
    {
       $data = [];
       foreach ($this->timetable as $timetable) {
            $data[$timetable->day."_open"] = $timetable->open ? Carbon::parse($timetable->open)->format('h:i a') : '';
            $data[$timetable->day."_close"] = $timetable->close ? Carbon::parse($timetable->close)->format('h:i a') : '';
       }
        return $data;
    }

    public function getIsTimetableEnteredAttribute()
    {      
        $result_count = PetProTimetable::where('pet_pro_id', $this->id)->whereNotNull('open')->count();
        if( $result_count ) {
            return true;
        }
        return false;

    }
}
