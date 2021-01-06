<?php

namespace App\Models;

use App\Models\PetProDealClaim;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetProDeal extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'formated_start_date', "formated_end_date", 'is_claimed'];

    public function petPro()
    {
        return $this->belongsTo('App\Models\PetPro', 'pet_pro_id', 'id');
    }

    public function claims()
    {
        return $this->hasMany('App\Models\PetProDealClaim', 'pet_pro_deal_id', 'id');
    }

    public function scopeActive($query)
    {
        $current_date_time_ob = Carbon::now('UTC');
        $current_date = $current_date_time_ob->format('Y-m-d');

        return $query->where('pet_pro_deals.status', 'active')
            ->where(function($q) use($current_date) {
                $q->where(function($q){
                    $q->whereNull('start_date')
                      ->whereNull('end_date');
                })->orWhere(function($q) use($current_date) {
                    $q->where('start_date', '<=', $current_date)
                      ->where('end_date', '>=', $current_date);
                });
            });
    }

    public function getIsClaimedAttribute()
    {
        $user = Auth::user();
        if ($user) {
            if ($user->count() != 0) {
                $isClaimed = PetProDealClaim::where('pet_pro_deal_id', $this->id)
                            ->where('user_id', $user->id)
                            ->first();
                if( $isClaimed ) {
                    return 1;
                }
            }
        }
        return 0;
    }

    //formated_start_date
    public function getFormatedStartDateAttribute()
    {
        $data = "";
        if(!empty($this->start_date)) {
            $data = Carbon::parse($this->start_date)->format('m/d/y');
        }
        return $data;
    }

    //formated_end_date
    public function getFormatedEndDateAttribute()
    {
        $data = "";
        if(!empty($this->end_date)) {
            $data = Carbon::parse($this->end_date)->format('m/d/y');
        }
        return $data;
    }
}
