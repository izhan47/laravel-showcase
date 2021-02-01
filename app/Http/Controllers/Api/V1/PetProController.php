<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Models\PetPro;
use App\Models\PetProCategory;
use App\Models\BusinessNature;
use App\Models\PetProDeal;
use App\Models\PetProDealClaim;
use App\Models\PetProReview;
use App\Models\UserLovedPetPro;
use Auth;
use Illuminate\Http\Request;
use Validator;
use Math;
use App\Models\PetProSelectedCategory;
use App\Models\PetProSelectedBusinessNature;
use DB;

class PetProController extends Controller
{
    public function __construct()
    {
        $this->statusCodes = config("wagenabled.status_codes");
        $this->responseData = [];
        $this->message = "Please, try again!";
        $this->code = config("wagenabled.status_codes.normal_error");
    }

    public function careFromBestList(Request $request)
    {
        $this->responseData["care_from_best_list"] = PetPro::withCount(['deals' => function($query){
                                                                    $query->active();
                                                                }])
                                                            ->with('coverImage')
                                                            ->orderBy('avg_rating', 'desc')
                                                            ->orderBy('id', 'desc')
                                                            ->take(config('wagenabled.no_of_care_from_best_display', 6))
                                                            ->get();

        foreach ($this->responseData["care_from_best_list"] as $key => $petPro) {
            $petPro['categories'] = PetProSelectedCategory::leftjoin('pet_pro_categories','pet_pro_categories.id','=','pet_pro_selected_categories.category_id')
                            ->select([
                                'pet_pro_categories.id',
                                'pet_pro_categories.name as name',
                            ])
                            ->where('pet_pro_selected_categories.pet_pro_id', '=', $petPro->id)
                            ->get();
        }
        $this->message = "";
        $this->code = $this->statusCodes['success'];

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getList(Request $request, $page = 1)
    {
        $input = $request->only(['latitude', 'longitude']);
        $perPage = config("wagenabled.per_page_pet_pro_results", 6);
        $skip = ($page > 1) ? ($perPage * ($page - 1)) : 0;

        $is_seach_by_location = false;

        $category_id = $request->get('category_id', "");
        $business_id = $request->get('business_id', "");
        $search = $request->get('search', "");
        $sort_by = $request->get('sort_by', "");

        $pet_pros = PetPro::withCount(['deals' => function($query){
                                        $query->active();
                                    },

                                ])
                            ->with('coverImage', 'city', 'state');
                            
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
               $selectedCategoryPetProIds = PetProSelectedCategory::whereIn('category_id', $categoryIdArray)->pluck('pet_pro_id')->toArray();
               $pet_pros = $pet_pros->whereIn('id', $selectedCategoryPetProIds);
            }
        }
 
        $business_id = json_decode($business_id);
        if($business_id != null && count($business_id) ) {
            $allvalue = false;
            $businessIdArray = [];
            foreach ($business_id as $key => $value) {
                if($value->value != ""){
                    $businessIdArray[] = $value->value;
                }else{
                    $allvalue = true;
                }
            }
            if(!$allvalue){
               $selectedBusinessPetProIds = PetProSelectedBusinessNature::whereIn('business_id', $businessIdArray)->pluck('pet_pro_id')->toArray();
               $pet_pros = $pet_pros->whereIn('id', $selectedBusinessPetProIds);
            }
        }

        if( $search ) {
            // $pet_pros = $pet_pros->Where('store_name', 'like', '%'.$search.'%' );
            $pet_pros = $pet_pros->where(function ($query) use ($search) {
              $query
              ->where('store_name', 'like', '%'.$search.'%' )
                      ->orWhere('description', 'like', '%'.$search.'%')
                      ->orWhereHas('servicesOffered', function($q) use($search) {
                          $q->where('pet_pro_services_offered.service', 'like', '%'.$search.'%'); // '=' is optional
                   });
          });
        }
        // if( $sort_by == 'nearest' ) {

            /*if( empty($input["longitude"]) || empty($input["latitude"]) ) {
               $user = Auth::user();
                if ($user) {
                    if($user->city) {
                        $input["longitude"] = $user->city->city_latitude;
                        $input["latitude"] = $user->city->city_longitude;
                    }
                }
            }*/
            if(!( empty($input["longitude"]) || empty($input["latitude"]) ) ) {
                $is_seach_by_location = true;
                //$pet_pros = $pet_pros->selectRaw('pet_pros.*,  ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$input["latitude"], $input["longitude"], $input["latitude"]]);
                $pet_pros_with_lat_long =  DB::table('pet_country_state_city')->selectRaw('pet_country_state_city.*,  ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$input["latitude"], $input["longitude"], $input["latitude"]]);
            }
        // }
        
        $totalRecords = $pet_pros->count();

        $this->responseData = [
            "pet_pro_list" => [],
		];

        if($totalRecords > 0) {

			$petProArr = clone $pet_pros;
			$totalRecords = count($petProArr->get());
            //if( $sort_by ) {
                switch ($sort_by) {
                    case 'popular':
                            $pet_pros =  $pet_pros->orderBy("avg_rating", "DESC")
                                                ->orderBy("id", "DESC");
                        break;

                    case 'deal_offered':
                            $pet_pros =  $pet_pros->orderBy("deals_count", "DESC")
                                                ->orderBy("id", "DESC");
                        break;

                    case 'latest':
                            $pet_pros =  $pet_pros->orderBy("id", "DESC");
                        break;

                    default:
                        $pet_pros =  $pet_pros->orderBy("id", "DESC");
                        break;
                }
            //}
            if( $is_seach_by_location ) {
                $pet_pro_list = $pet_pros->get();
            }else{
                $pet_pro_list = $pet_pros->skip($skip)
                                    ->take($perPage)
                                    ->get();
            }
                                   

            $this->responseData["pet_pro_list"] = $pet_pro_list;

            foreach ($this->responseData["pet_pro_list"] as $key => $petPro) {
                $petPro['categories'] = PetProSelectedCategory::leftjoin('pet_pro_categories','pet_pro_categories.id','=','pet_pro_selected_categories.category_id')
                                ->select([
                                    'pet_pro_categories.id',
                                    'pet_pro_categories.name as name',
                                ])
                                ->where('pet_pro_selected_categories.pet_pro_id', '=', $petPro->id)
                                ->get();
			}
		}
        if( $is_seach_by_location ) {
            $pet_pros_with_lat_long =  $pet_pros_with_lat_long->orderBy(\DB::raw('-`distance`'), 'desc')->havingRaw('distance <= ?',[25]);//->orHavingRaw('distance is null')
            $this->responseData["pet_pro_list"] = $this->addMatchOne($pet_pros_with_lat_long->get(), $this->responseData["pet_pro_list"]);
            $totalPages = ceil(count($this->responseData["pet_pro_list"]) / $perPage);

            $this->responseData["total_page"] = $totalPages;
            $this->responseData["total_records"] = count($this->responseData["pet_pro_list"]);
            $skiparray = 0;
            $limitarray = $perPage;
            if($page>1){
               $skiparray = ($perPage*$page)-$perPage;
                $limitarray =  ($perPage*$page)-1;
            }
            $this->responseData["pet_pro_list"] =array_slice( $this->responseData["pet_pro_list"], $skiparray,  $limitarray); 
            $this->message = "";
            $this->code = $this->statusCodes['success'];
    
            return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
        }
        else{
           
        $totalPages = ceil($totalRecords / $perPage);
        $this->responseData["total_page"] = $totalPages;
        $this->responseData["total_records"] = $totalRecords;

        $this->message = "";
        $this->code = $this->statusCodes['success'];

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
        }

    }

    public function getMapList(Request $request )
    {
		$input = $request->only(['latitude', 'longitude']);
        $is_seach_by_location = false;

        $category_id = $request->get('category_id', "");
        $search = $request->get('search', "");
		
		$pet_pros = PetPro::withCount(['deals' => function($query){
										$query->active();
									},
									])
									->with('coverImage');

		if( $category_id ) {
			$selectedCategoryPetProIds = PetProSelectedCategory::where('category_id', $category_id)->pluck('pet_pro_id')->toArray();
			$pet_pros = $pet_pros->whereIn('id', $selectedCategoryPetProIds);
		}

		if( $search ) {
			$pet_pros = $pet_pros->where(function ($query) use ($search) {
				$query
				->where('store_name', 'like', '%'.$search.'%' )
				->orWhere('description', 'like', '%'.$search.'%')
				->orWhereHas('servicesOffered', function($q) use($search) {
				$q->where('pet_pro_services_offered.service', 'like', '%'.$search.'%'); // '=' is optional
				});
			});
		}
		
		if(!( empty($input["longitude"]) || empty($input["latitude"]) ) ) {
			$is_seach_by_location = true;
			$pet_pros = $pet_pros->selectRaw('pet_pros.*,  ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$input["latitude"], $input["longitude"], $input["latitude"]])->orderBy(\DB::raw('-`distance`'), 'desc')->havingRaw('distance <= ?',[25])->orHavingRaw('distance is null');
		}

		$pet_pro_list = $pet_pros->get();

        $this->responseData["pet_pro_list"] = $pet_pro_list;
        $this->message = "";
        $this->code = $this->statusCodes['success'];

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getDetails(Request $request, $slug) {

        $pet_pro = PetPro::with(['country', 'state', 'city', 'otherImage', 'coverImage', 'images', 'servicesOffered',
                                    'deals' => function($query){
                                        $query->active();
                                    },
                                    'events' => function($query){
                                        $query->active();
                                    }
                                ])
                                ->Where('slug', $slug)
                                ->first();
        if( $pet_pro ) {
            $pet_pro['categories'] = PetProSelectedCategory::leftjoin('pet_pro_categories','pet_pro_categories.id','=','pet_pro_selected_categories.category_id')
            ->select([
                'pet_pro_categories.id',
                'pet_pro_categories.name as name',
            ])
            ->where('pet_pro_selected_categories.pet_pro_id', '=', $pet_pro->id)
            ->get();

            $this->responseData["is_liked"] = 0;
            $user = Auth::user();
            if ($user) {
                if ($user->count() != 0) {
                    $this->responseData["is_liked"] = UserLovedPetPro::where("user_id", $user->id)->where("pet_pro_id", $pet_pro->id)->first() ? 1 : 0;
                }
            }

            $this->responseData["per_pro"] = $pet_pro;
            $this->message = "";
            $this->code = $this->statusCodes['success'];
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);

    }

    public function likeDislikePetPro(Request $request, $slug) {

        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $pet_pro = PetPro::Where('slug', $slug)
                                ->first();
        if( $pet_pro ) {
            $isLoved = UserLovedPetPro::where('user_id', $user->id)
                                        ->where('pet_pro_id', $pet_pro->id)
                                        ->first();
            if( $isLoved ) {
                $isLoved->delete();
                $this->responseData["is_liked"] = 0;
                $this->message = "Pet pro removed from your profile";
            } else {
                $isSaved = UserLovedPetPro::create([
                    'user_id' => $user->id,
                    'pet_pro_id' => $pet_pro->id
                ]);
                $this->responseData["is_liked"] = 1;
                $this->message = "Pet pro saved to your profile";
            }
            $this->code = $this->statusCodes['success'];
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function storeReview(Request $request, $slug) {

        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'rate' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $pet_pro = PetPro::Where('slug', $slug)
                                ->first();
        if( $pet_pro ) {
            $input = $request->only(['rate', 'description']);
            $input["user_id"] = $user->id;
            $input["name"] = $user->name;
            $input["pet_pro_id"] = $pet_pro->id;
            $isSaved = PetProReview::create($input);
            if ($isSaved) {

                $no = 1;
                if( $pet_pro->avg_rating != 0  ) {
                    $no = ( $pet_pro->total_rated / $pet_pro->avg_rating ) + 1;
                }
                $total_rated = $pet_pro->total_rated + $input["rate"];
                $avg_rating = ($total_rated / $no);

                $pet_pro_details["total_rated"] = $total_rated;
                $pet_pro_details["avg_rating"] = $avg_rating;
                $pet_pro->update($pet_pro_details);

                $this->responseData["avg_rating"] = round($avg_rating, 2);
                $this->responseData["pet_pro_review"] = [PetProReview::with('user')
                                    ->where("id", $isSaved->id)
                                    ->first()];

                $this->message = "Thank you for leaving a review!";
                $this->code = $this->statusCodes['success'];
            }
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getReviewList(Request $request, $slug, $lastId = 0)
    {
        $perPage = config("wagenabled.no_of_review_display", 3);
        $pet_pro = PetPro::Where('slug', $slug)
                                ->first();
        if( $pet_pro ) {

            $this->responseData["is_more_review"] = 0;
            $this->responseData["pet_pro_reviews"] = [];
            $this->responseData["reviews_count"] = 0;
            $this->responseData["total_records"] = 0;

            if( $lastId == 0 ) {
                $last_pet_pro_review = PetProReview::select('id')
                                ->where("pet_pro_id", $pet_pro->id)
                                ->orderBy('id', 'desc')
                                ->first();
                if( $last_pet_pro_review ){
                    $lastId = $last_pet_pro_review->id;
                    $lastId++;
                }
            }

            if( $lastId != 0 ) {

                $reviews = PetProReview::with('user')
                                    ->where("pet_pro_id", $pet_pro->id);

                $reviews_count = clone $reviews;
                $reviews_count = $reviews_count->count();

                $reviews = $reviews->where("id", "<", $lastId)
                                    ->orderBy("id", "desc")
                                    ->take($perPage)
                                    ->get();

                $last_record = PetProReview::where("pet_pro_id", $pet_pro->id)
                                                ->where("id", "<", $lastId)
                                                ->orderBy("id", "desc")
                                                ->skip($perPage)
                                                ->first();

                $is_more_records = ($last_record) ? 1 : 0;

                $this->responseData["is_more_review"] = $is_more_records;
                $this->responseData["pet_pro_reviews"] = $reviews;
                $this->responseData["reviews_count"] = $reviews->count();
                $this->responseData["total_records"] = $reviews_count;

            }

            $this->message = "";
            $this->code = $this->statusCodes['success'];
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function deleteReview(Request $request, $slug, $id) {

        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $pet_pro = PetPro::Where('slug', $slug)
                                ->first();
        if( $pet_pro ) {
            $petProReview = PetProReview::where('user_id', $user->id)
                                        ->where('pet_pro_id', $pet_pro->id)
                                        ->where('id', $id)
                                        ->first();
            if( $petProReview ) {
                $no = 1;
                if( $pet_pro->avg_rating != 0  ) {
                    $no = ( $pet_pro->total_rated / $pet_pro->avg_rating ) - 1;
                }
                $total_rated = $pet_pro->total_rated - $petProReview->rate;
                if( $no ) {
                    $avg_rating = ($total_rated / $no);
                } else {
                    $avg_rating= $total_rated;
                }

                $pet_pro_details["total_rated"] = $total_rated;
                $pet_pro_details["avg_rating"] = $avg_rating;

                $pet_pro->update($pet_pro_details);
                $petProReview->delete();

                $this->responseData["avg_rating"] = round($avg_rating, 2);
                $this->message = "review deleted successfully";
                $this->code = $this->statusCodes['success'];
            }
        }
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function claimDeal(Request $request, $slug, $pet_deal_id) {

        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $pet_pro = PetPro::Where('slug', $slug)
                                ->first();
        if( $pet_pro ) {
           $pet_pro_deal = PetProDeal::where('pet_pro_id', $pet_pro->id)
                                        ->find($pet_deal_id);
            if( $pet_pro_deal ) {

                $isClaimed = PetProDealClaim::where('user_id', $user->id)
                                        ->where('pet_pro_deal_id', $pet_pro_deal->id)
                                        ->first();
                if( ! $isClaimed ) {
                    $input["user_id"] = $user->id;
                    $input["pet_pro_deal_id"] = $pet_pro_deal->id;
                    $isSaved = PetProDealClaim::create($input);
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

    public function getCategoryList(Request $request) {

        $category_data = PetProCategory::select(['id', 'name'])
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

    public function getBusinessNatureList(Request $request) {

        $business_nature_data = BusinessNature::select(['id', 'name'])
                                            ->orderBy('name')
                                            ->get();

        $business_nature_list = [];
        $business_nature_list[] = ["value" => '', "label"=> 'All'];
        foreach ($business_nature_data as $business) {
            $business_nature_list[] = [ "value" => $business->id, "label"=> $business->name];
        }

        $this->responseData["business_nature_list"] = $business_nature_list;
        $this->message = "";
        $this->code = $this->statusCodes['success'];

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);

    }

    public function addMatchOne($petProsLatLongArr, $petProsArr)
    {   $petProsLatestArray = [];
        foreach ($petProsArr as $key1=>$match) {
           foreach ($petProsLatLongArr as $key => $value) {
             
             if($match->id == $value->pet_pro_id){
                $petProsLatestArray[] = $match;
             }
           }
        }

        return json_decode(json_encode($petProsLatestArray));
    }

    public function addLocationAnotherTable()
    {
        $pet_pros = PetPro::all();
        foreach ($pet_pros as $key => $value) {
            if($value->state_id && $value->city_id){
                DB::table('pet_country_state_city')->insert([
                    'pet_pro_id' => $value->id,
                    'country_id' => 231,
                    'state_id' => $value->state_id,
                    'city_id' =>  $value->city_id,
                    'latitude' => $value->latitude,
                    'longitude' => $value->longitude,
                ]);
            }
        }
    }

}
