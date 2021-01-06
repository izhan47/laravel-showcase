<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Models\PetPro;
use App\Models\PetProDeal;
use App\Models\PetProDealClaim;
use App\Models\User;
use App\Models\WatchAndLearnDeal;
use App\Models\WatchAndLearnDealClaim;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{   
    public function index()
    {      
        $monthStart = Carbon::today()->startOfMonth()->format('m-d-Y');            
        $monthEnd = Carbon::now()->format('m-d-Y');   

        $users_count = User::count();
        $pet_pros_count = PetPro::count();
        $pet_pro_deals_count = PetProDeal::count();
        $pet_pro_deal_claimed_count = PetProDealClaim::count();
        $watch_and_learn_deals_count = WatchAndLearnDeal::count();
        $watch_and_learn_deal_claimed_count = WatchAndLearnDealClaim::count();

        return view('admin.main.dashboard', compact('users_count', 'pet_pros_count', 'pet_pro_deals_count', 'pet_pro_deal_claimed_count', 'monthStart', 'monthEnd', 'watch_and_learn_deals_count', 'watch_and_learn_deal_claimed_count'));
    }

    public function getUsersGraphData(Request $request)
    { 
        $code = config("wagenabled.status_codes.normal_error");
        $message = "";
        $graph_data = [];
        $graph_key = [];
        $graph_value = [];

        try {

            $monthStart = Carbon::createFromFormat('m-d-Y', $request->get('monthStart'));           
            $monthEnd = Carbon::createFromFormat('m-d-Y', $request->get('monthEnd')); 
            
            $dayCount = $monthEnd->diffInDays($monthStart);
           
            $today = Carbon::today();
            $start = $monthStart; 

            for ($i = 0 ; $i <= $dayCount; $i++) {   
                $dates[$start->copy()->addDays($i)->format('m-d-Y')] = 0;         
                $graph_key[] = $start->copy()->addDays($i)->format('m-d-y');         
            } 

            $user_raw = User::select([DB::raw('DATE_FORMAT(created_at,  "%m-%d-%Y") as date') ,DB::raw('count(id) as total_users')])->whereDate('created_at', $monthEnd)->first();   
          
            //monthly uploaded apps
            $user_monthly_raw = User::select(["*", DB::raw('DATE_FORMAT(created_at, "%m-%d-%Y") as date'),DB::raw('count(id) as total_count') ])->whereBetween('created_at', [ $monthStart->setTime(0,0)->format('Y-m-d H:i:s'), $monthEnd->setTime(0,0)->format('Y-m-d H:i:s') ] )->groupBy("date")->orderBy('date', 'asc')->get()->toArray();
            
            $users_monthly = array(); 
            foreach($user_monthly_raw as $month_array) {
                $users_monthly[$month_array["date"]] = $month_array["total_count"];
            }  
            if( $user_raw->date ) {
                $users_monthly[$user_raw->date] = $user_raw->total_users;            
            }

            $graph_value = (array_values(array_replace($dates, $users_monthly)) );        
            $graph_data['graph_key'] = $graph_key;
            $graph_data['graph_value'] = $graph_value;        
            $code = config("wagenabled.status_codes.success");

        }
        catch (Exception $e) {
            $message = "Please, try again!";
        }

        return WagEnabledHelpers::apiJsonResponse($graph_data, $code, $message);
    }
    
}
