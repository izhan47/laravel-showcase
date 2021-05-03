<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\HubspotHelpers;
use App\Http\WagEnabledHelpers;
use App\Models\Breed;
use App\Models\City;
use App\Models\Country;
use App\Models\PetProDeal;
use App\Models\PetProDealClaim;
use App\Models\PetProReview;
use App\Models\PetProSelectedCategory;
use App\Models\User;
use App\Models\UserLovedPetPro;
use App\Models\UserPet;
use App\Models\UserSavedVideo;
use App\Models\UsersPetBreed;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->statusCodes = config("wagenabled.status_codes");
        $this->responseData = [];
        $this->message = "Please, try again!";
        $this->code = config("wagenabled.status_codes.normal_error");
    }

    public function getProfileDetails(Request $request)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $user = User::with('city', 'state')->find($user->id);

        $this->responseData["user_details"] = $user;
        $this->code = $this->statusCodes['success'];
        $this->message = '';

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required | max: 255',
            'email' => [
                "required",
                "email",
                Rule::unique('users')->ignore($user->id),
            ],
            'zipcode' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'old_password' => 'nullable|min:6',
            'password' => 'required_with:old_password|confirmed|min:6',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['name', 'email', 'zipcode', 'latitude', 'longitude']);

        $city = City::where('zipcode', $input["zipcode"])->first();
        if (!$city) {
            $validator->getMessageBag()->add('zipcode', 'Please enter correct zipcode');
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input['city_id'] = $city->id;
        $input['state_id'] = $city->state->id;
        $input['country_id'] = $city->state->country->id;

        $old_password = $request->get("old_password", "");
        $password = $request->get("password", "");

        if ($old_password) {
            if (!Hash::check($old_password, $user->password)) {
                $validator->getMessageBag()->add('old_password', 'Please enter correct password.');
                return WagEnabledHelpers::apiValidationFailResponse($validator);
            }
            $input["password"] = Hash::make($password);
        }

        if ($request->file('image', false)) {
            $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.user_profile_image_path"), $user->profile_image, $isCreateThumb = "1", $height = 250, $width = 380);
            if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                $input["profile_image"] = $imageStore['name'];
            }
        }

        $isSaved = $user->update($input);
        if ($isSaved) {
            $this->responseData["user_details"] = $user;
            $this->code = $this->statusCodes['success'];
            $this->message = 'Profile Updated successfully';
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['latitude', 'longitude']);
        $isSaved = $user->update($input);

        if ($isSaved) {
            $this->responseData["user_details"] = $user;
            $this->code = $this->statusCodes['success'];
            $this->message = '';
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function storeMyPets(Request $request)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required | max: 255',
            'breed_ids' => 'required',
            'pet_image' => 'required | mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['name']);

        if ($request->file('pet_image', false)) {
            $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('pet_image'), config("wagenabled.path.doc.users_pet_image_path"), '', $isCreateThumb = "1", $height = 300, $width = 350);
            if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                $input["pet_image"] = $imageStore['name'];
            }
        }

        $input["user_id"] = $user->id;
        $isSaved = UserPet::create($input);

        if ($isSaved) {
            // insert breed
            $breedIds = [];
            if (!empty($request->get("breed_ids"))) {
                $breedIds = explode(",", $request->get("breed_ids"));
            }

            foreach ($breedIds as $breedId) {
                if ($breedId) {
                    $user_breed = UsersPetBreed::insert([
                        "users_pet_id" => $isSaved->id,
                        "breed_id" => $breedId,
                    ]);
                }
            }

            $users_pets = UserPet::with('breed')
                ->where('id', $isSaved->id)
                ->get();
            $this->responseData["users_pet"] = $users_pets;
            $this->code = $this->statusCodes['success'];
            $this->message = 'Pet added successfully.';
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function deleteMyPet(Request $request, $id)
    {

        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $userPet = UserPet::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if ($userPet) {
            $petBreed = UsersPetBreed::where('users_pet_id', $id)->delete();
            $userPet->delete();

            $this->message = "Pet deleted successfully";
            $this->code = $this->statusCodes['success'];
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function completeProfile(Request $request)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'country_id' => 'required | integer',
            'zipcode' => 'required',
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['country_id', 'zipcode']);

        $city = City::where('zipcode', $input["zipcode"])->first();
        if (!$city) {
            $validator->getMessageBag()->add('zipcode', 'Please enter correct zipcode');
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['country_id', 'zipcode']);
        $input['city_id'] = $city->id;
        $input['state_id'] = $city->state->id;

        $isSaved = $user->update($input);

        if ($isSaved) {
            // entry
            $arr = array(
                'properties' => array(
                    array(
                        'property' => 'zip',
                        'value' => $user->zipcode,
                    ),
                ),
            );
            $post_json = json_encode($arr);

            HubspotHelpers::updateContact($post_json, $user->email, env('HUBSPOT_API_KEY'));

            $this->responseData["user_details"] = $user;
            $this->code = $this->statusCodes['success'];
            $this->message = 'Profile updated successfully';
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function updateVetDetails(Request $request)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'vet_place_name' => 'required',
            'vet_address' => 'required',
            'vet_phone_number' => 'required',
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->only(['vet_place_name', 'vet_address', 'vet_phone_number']);
        $isSaved = $user->update($input);
        if ($isSaved) {
            $this->responseData["user_details"] = $user;
            $this->code = $this->statusCodes['success'];
            $this->message = 'Profile updated successfully';
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getLovedPetPros(Request $request, $lastId = 0)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $perPage = config("wagenabled.no_of_loved_pet_pro_display", 3);

        $loved_pet_pros = UserLovedPetPro::with(['petPro' => function ($query) {
            $query->withCount('deals');
        }, 'petPro.coverImage', 'petPro.deals'])
            ->where('user_id', $user->id);
        if ($lastId) {
            $loved_pet_pros = $loved_pet_pros->where('id', '<', $lastId);
        }

        $loved_pet_pros = $loved_pet_pros->orderBy('id', 'desc')
            ->take($perPage)
            ->get();

        if ($loved_pet_pros->count() > 0) {
            foreach ($loved_pet_pros as $key => $petPro) {
                $petPro['categories'] = PetProSelectedCategory::leftjoin('pet_pro_categories', 'pet_pro_categories.id', '=', 'pet_pro_selected_categories.category_id')
                    ->select([
                        'pet_pro_categories.id',
                        'pet_pro_categories.name as name',
                    ])
                    ->where('pet_pro_selected_categories.pet_pro_id', '=', $petPro->pet_pro_id)
                    ->get();
            }

            $get_previous_records = UserLovedPetPro::where('id', '<', $loved_pet_pros->last()->id)
                ->where('user_id', $user->id)
                ->first();
            $this->responseData["is_more_loved_pet_pros"] = ($get_previous_records ? 1 : 0);
        }

        $this->responseData["loved_pet_pros"] = $loved_pet_pros;
        $this->code = $this->statusCodes['success'];
        $this->message = '';

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getSavedVideos(Request $request, $page = 1)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $perPage = config("wagenabled.no_of_saved_video_display", 3);
        $skip = ($page > 1) ? ($perPage * ($page - 1)) : 0;

        $saved_videos = UserSavedVideo::with('watchAndLearn', 'watchAndLearn.category')
            ->whereHas('watchAndLearn', function ($query) {
                $query->Where('status', 'published');
                $query->GetWatchAndLearnCategory();
            })
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->skip($skip)
            ->take($perPage)
            ->get();

        if ($saved_videos->count() > 0) {
            $get_previous_records = UserSavedVideo::with('watchAndLearn', 'watchAndLearn.category')
                ->whereHas('watchAndLearn', function ($query) {
                    $query->Where('status', 'published');
                    $query->GetWatchAndLearnCategory();
                })
                ->where('id', '<', $saved_videos->last()->id)
                ->where('user_id', $user->id)
                ->first();
            $this->responseData["is_more_saved_videos"] = ($get_previous_records ? 1 : 0);
        }

        $this->responseData["saved_videos"] = $saved_videos;
        $this->code = $this->statusCodes['success'];
        $this->message = '';

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getSavedProductReview(Request $request, $page = 1)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $perPage = config("wagenabled.no_of_saved_video_display", 3);
        $skip = ($page > 1) ? ($perPage * ($page - 1)) : 0;

        $saved_videos = UserSavedVideo::with(['watchAndLearn' => function ($query) {
            $query->withCount('deals');
        }, 'watchAndLearn.category', 'watchAndLearn.deals'])
            ->whereHas('watchAndLearn', function ($query) {
                $query->Where('status', 'published');
                $query->ProductReviewCategory();
            })
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->skip($skip)
            ->take($perPage)
            ->get();

        if ($saved_videos->count() > 0) {
            $get_previous_records = UserSavedVideo::with('watchAndLearn', 'watchAndLearn.category')
                ->whereHas('watchAndLearn', function ($query) {
                    $query->Where('status', 'published');
                    $query->ProductReviewCategory();
                })
                ->where('id', '<', $saved_videos->last()->id)
                ->where('user_id', $user->id)
                ->first();
            $this->responseData["is_more_saved_videos"] = ($get_previous_records ? 1 : 0);
        }

        $this->responseData["saved_videos"] = $saved_videos;
        $this->code = $this->statusCodes['success'];
        $this->message = '';

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getUserPetProReview(Request $request, $page = 1)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $perPage = config("wagenabled.no_of_user_pet_pro_review_display", 3);
        $skip = ($page > 1) ? ($perPage * ($page - 1)) : 0;

        $pet_pro_reviews = PetProReview::with('user', 'petPro', 'petPro.coverImage')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->skip($skip)
            ->take($perPage)
            ->get();

        if ($pet_pro_reviews->count() > 0) {
            $get_previous_records = PetProReview::where('id', '<', $pet_pro_reviews->last()->id)
                ->where('user_id', $user->id)
                ->first();
            $this->responseData["is_more_pet_pro_reviews"] = ($get_previous_records ? 1 : 0);
        }

        $this->responseData["pet_pro_reviews"] = $pet_pro_reviews;
        $this->code = $this->statusCodes['success'];
        $this->message = '';

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getUsersPet(Request $request, $lastId = 0)
    {
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $perPage = config("wagenabled.no_of_users_pet_display", 1);

        $users_pets = UserPet::with('breed')
            ->where('user_id', $user->id);

        if ($lastId) {
            $users_pets = $users_pets->where('id', '<', $lastId);
        }

        $users_pets = $users_pets->orderBy('id', 'desc')
            ->take($perPage)
            ->get();

        if ($users_pets->count() > 0) {
            $get_previous_records = UserPet::where('id', '<', $users_pets->last()->id)
                ->where('user_id', $user->id)
                ->first();
            $this->responseData["is_more_users_pets"] = ($get_previous_records ? 1 : 0);
        }

        $this->responseData["users_pets"] = $users_pets;
        $this->code = $this->statusCodes['success'];
        $this->message = '';

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getBreedList(Request $request)
    {

        $breed_data = Breed::select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $breed_list = [];
        $breed_list[] = ["value" => '', "label" => 'Mixed'];
        foreach ($breed_data as $breed) {
            $breed_list[] = ["value" => $breed->id, "label" => $breed->name];
        }

        $this->responseData["breed_list"] = $breed_list;
        $this->message = "";
        $this->code = $this->statusCodes['success'];

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);

    }

    public function getCountryList(Request $request)
    {
        $country_data = Country::select(['id', 'name'])
            ->orderBy('name')
            ->get();
        $country_list = [];
        $country_list[] = ["value" => '', "label" => 'None'];
        foreach ($country_data as $country) {
            $country_list[] = ["value" => $country->id, "label" => $country->name];
        }
        $this->responseData["country_list"] = $country_list;
        $this->message = "";
        $this->code = $this->statusCodes['success'];
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getStates(Request $request, $country_id)
    {
        $code = config("wagenabled.status_codes.normal_error");
        $message = "";
        $states = [];
        try {
            $states = State::select(["id", "name"])->where('country_id', $country_id)->orderBy('name', 'asc')->get();
            $code = config("wagenabled.status_codes.success");
        } catch (Exception $e) {
            $message = "Please, try again!";
        }
        return WagEnabledHelpers::apiJsonResponse($states, $code, $message);
    }

    public function getCities(Request $request, $state_id)
    {
        $code = config("wagenabled.status_codes.normal_error");
        $message = "";
        $cities = [];
        try {
            $cities = City::select(["id", "state_id", "name"])->where('state_id', $state_id)->orderBy('name')->orderBy('id')->groupBy('name')->get();
            $code = config("wagenabled.status_codes.success");
        } catch (Exception $e) {
            $message = "Please, try again!";
        }
        return WagEnabledHelpers::apiJsonResponse($cities, $code, $message);
    }

    public function getclaimDeals()
    {

        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }
        $isClaimed = PetProDealClaim::select('pet_pro_deal_id')->where('user_id', $user->id)->get();
        $deal_ids = [];
        foreach ($isClaimed as $key => $value) {
            $deal_ids[] = $value->pet_pro_deal_id;
        }
        $this->responseData['claimedDeals'] = PetProDeal::whereIn('id', $deal_ids)->get();
        $this->message = "";
        $this->code = $this->statusCodes['success'];
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

}
