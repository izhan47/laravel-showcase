<?php

namespace App\Models;

use App\Models\WatchAndLearnDealClaim;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WatchAndLearnDeal extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [ 'formated_start_date', "formated_end_date", 'is_claimed'];

    public function watchAndLearnDeal()
    {
        return $this->belongsTo('App\Models\WatchAndLearn', 'watch_and_learn_id', 'id');
    }

    public function claims()
    {
        return $this->hasMany('App\Models\WatchAndLearnDealClaim', 'watch_and_learn_deal_id', 'id');
    }

    public function scopeActive($query)
    {
        $current_date_time_ob = Carbon::now('UTC');
        $current_date = $current_date_time_ob->format('Y-m-d');

        return $query->where('watch_and_learn_deals.status', 'active')
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
                $isClaimed = WatchAndLearnDealClaim::where('watch_and_learn_deal_id', $this->id)
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
