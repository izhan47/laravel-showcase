<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Mail\SendCommentNotificaitonMail;
use App\Models\UserSavedVideo;
use App\Models\WatchAndLearn;
use App\Models\WatchAndLearnCategory;
use App\Models\WatchAndLearnComment;
use App\Models\WatchAndLearnDeal;
use App\Models\WatchAndLearnDealClaim;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class ProductReviewController extends Controller
{          
    public function __construct()
    {   
        $this->statusCodes = config("wagenabled.status_codes");
        $this->responseData = [];
        $this->message = "Please, try again!";
        $this->code = config("wagenabled.status_codes.normal_error");
        $this->watch_and_learn_comment_ids = [];
    }

    public function getList(Request $request, $page = 1) 
    {         
        $perPage = config("wagenabled.per_page_watch_and_learn_results", 6);        
        $skip = ($page > 1) ? ($perPage * ($page - 1)) : 0;   

        $category_id = $request->get('category_id', "");   
        $search = $request->get('search', "");   
        $sort_by = $request->get('sort_by', "");   

        $watch_and_learn = WatchAndLearn::with('category')->withCount(['deals' => function($query){
                                        $query->active();
                                    }, 

                                ])->ProductReviewCategory()->withCount('users')->Where('status', 'published');  
        $category_id = json_decode($category_id);
        if($category_id != null && count($category_id) ) {
            $allCatValue = false;
            $categoryIdArray = [];
            foreach ($category_id as $key => $value) {
                if($value->value != ""){
                    $categoryIdArray[] = $value->value;
                }else{
                    $allCatValue = true;
                }
            }
            if(!$allCatValue){
                $watch_and_learn = $watch_and_learn->whereIn( 'category_id', $categoryIdArray );
            }
        }

        if( $search ) {
            $watch_and_learn = $watch_and_learn->where( function($query) use( $search ){ 
                                            $query->Where('title', 'like', '%'.$search.'%');
                                            $query->orWhere('description', 'REGEXP', '>[^<]*'.$search);
                                        });
        }

        $totalRecords = $watch_and_learn->count();
        $totalPages = ceil($totalRecords / $perPage); 

        $this->responseData = [
            "total_records" => $totalRecords,
            "total_page" => $totalPages,
            "watch_and_learn_list" => [],
        ];

        if($totalRecords > 0) {  
            
            switch ($sort_by) {
                case 'popular':                        
                        $watch_and_learn =  $watch_and_learn->orderBy("users_count", "DESC")
                                            ->orderBy("id", "DESC");
                    break;
                                   
                case 'latest': 
                        $watch_and_learn =  $watch_and_learn->orderBy("id", "DESC");
                    break;

                case 'deal_offered':
                        $watch_and_learn =  $watch_and_learn->orderBy("deals_count", "DESC")
                                                ->orderBy("id", "DESC");
                        break;
                default:                        
                    $watch_and_learn =  $watch_and_learn->orderBy("id", "DESC");
                    break;
            }

            $watch_and_learn_list = $watch_and_learn->skip($skip)
                                    ->take($perPage)
                                    ->get();  

            $this->responseData["watch_and_learn_list"] = $watch_and_learn_list;
        }

        $this->message = "";
        $this->code = $this->statusCodes['success']; 
       
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getCategoryList(Request $request) {

        $category_data = WatchAndLearnCategory::ProductReviewCategory()
                                            ->select(['id', 'name'])
                                            ->orderBy('name')
                                            ->get();

        $category_list = [];            
        $category_list[] = ["value" => '', "label"=> 'All'];            
        foreach ($category_data as $category) {
            $category_list[] = [ "value" => $category->id, "label"=> $category->name];
        }
        
        $this->responseData["category_list"] = $category_list;
        $this->message = "";
        $this->code = $this->statusCodes['success']; 
      
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);

    }

     public function claimDeal(Request $request, $slug, $watch_and_learn_deal_id) {
        
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }
        
        $watch_and_learn = WatchAndLearn::Where('slug', $slug)
                                ->first();
        if( $watch_and_learn ) {          
           $watch_and_learn_deal = WatchAndLearnDeal::where('watch_and_learn_id', $watch_and_learn->id)
                                        ->find($watch_and_learn_deal_id);
            if( $watch_and_learn_deal ) {                

                $isClaimed = WatchAndLearnDealClaim::where('user_id', $user->id)
                                        ->where('watch_and_learn_deal_id', $watch_and_learn_deal->id)
                                        ->first();
                if( ! $isClaimed ) {
                    $input["user_id"] = $user->id;
                    $input["watch_and_learn_deal_id"] = $watch_and_learn_deal->id;
                    $isSaved = WatchAndLearnDealClaim::create($input);                
                    if ($isSaved) {
                        $this->message = "Deal claimed successfully";            
                        $this->code = $this->statusCodes['success'];                 
                    }
                } 
                else {
                    $this->message = "Already claimed this deal";            
                }
            }
        }
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

}
