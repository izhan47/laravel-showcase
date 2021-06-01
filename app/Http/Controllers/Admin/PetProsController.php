<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PetProRequest;
use App\Http\WagEnabledHelpers;
use App\Library\GoogleMapHelper;
use App\Models\BusinessNature;
use App\Models\City;
use App\Models\Country;
use App\Models\PetPro;
use App\Models\PetProCategory;
use App\Models\PetProGallery;
use App\Models\PetProServicesOffered;
use App\Models\PetProTimetable;
use App\Models\PetType;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class PetProsController extends Controller
{
    public function __construct(PetPro $model)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $this->moduleName = "Pet Pros";
        $this->singularModuleName = "Pet Pro";
        $this->moduleRoute = url('admin/pet-pros');
        $this->moduleView = "admin.main.pet-pros";
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('module_route', $this->moduleRoute);
        View::share('moduleView', $this->moduleView);
    }

    public function index()
    {
        // $result = $this->model->where('status', 'approved')->with('categories', 'city', 'state')->orderBy('id', 'desc')->get();
        // dd($result);
        view()->share('isIndexPage', true);
        return view("$this->moduleView.index");
    }

    public function getDatatable(Request $request)
    {
        $result = $this->model->where('status', 'approved')->with('categories', 'city', 'state')->orderBy('id', 'desc')->get();
        return Datatables::of($result)
            ->addColumn('city_state', function ($result) {
                $str = "";
                if ($result->city_id) {
                    $str .= $result->city->name;
                }
                if ($result->state_id) {
                    $str .= ', ' . $result->state->name;
                }
                return $str;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        $countries = Country::pluck('name', 'id')->toArray();
        $states = [];
        $cities = [];

        $categories = PetProCategory::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        $businessNatures = BusinessNature::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        $petType = PetType::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        return view("$this->moduleView.create", compact('categories', 'countries', 'states', 'cities', 'businessNatures', 'petType'));
    }

    public function store(PetProRequest $request)
    {

        $pet_pro_input = $request->only(['store_name', 'website_url', 'email', 'phone_number', 'address_line_1', 'address_line_2', 'postal_code', 'description']);
        $time_input = $request->only(['monday_open', 'monday_close', 'tuesday_open', 'tuesday_close', 'wednesday_open', 'wednesday_close', 'thursday_open', 'thursday_close', 'friday_open', 'friday_close', 'saturday_open', 'saturday_close', 'sunday_open', 'sunday_close']);
        $inputCategories = $request['category_id'];
        $inputBusinessNatures = $request['business_id'];
        $inputPetType = $request['pet_type_id'];

        try {
            if ($request->get('is_featured_pet_pro')) {
                $pet_pro_input['is_featured_pet_pro'] = 1;
                $pet_pro_input['featured_title'] = $request->get('featured_title');
                $pet_pro_input['featured_description'] = $request->get('featured_description');
            }

            $isSaved = $this->model->create($pet_pro_input);
            $pet_pro = $this->model->where('id', $isSaved->id)->first();
            if ($request->country_id && $request->state_id && $request->city_id) {
                foreach ($request->country_id as $index => $row) {
                    $city_latitude = null;
                    $city_longitude = null;
                    $city = City::where('id', $request->city_id[$index])->first();
                    if ($city) {
                        $city_latitude = $city->city_latitude;
                        $city_longitude = $city->city_longitude;
                    }
                    $pet_pro->countries()->attach($row, ['state_id' => $request->state_id[$index], 'city_id' => $request->city_id[$index], 'latitude' => $city_latitude, 'longitude' => $city_longitude]);
                }
            }
            // $city =  City::where('id',$isSaved->city_id)->first();
            // if($city){
            //     $this->model->where('id', $id)->update([
            //         "latitude" => $city->city_latitude,
            //         "longitude" => $city->city_longitude,
            //     ]);
            // }
            if ($isSaved) {
                if (count($inputCategories)) {
                    $currentTime = Carbon::now();
                    $insertArray = [];
                    foreach ($inputCategories as $categoryId) {
                        $insertArray[] = [
                            "pet_pro_id" => $isSaved->id,
                            "category_id" => $categoryId,
                            "created_at" => $currentTime,
                            "updated_at" => $currentTime,
                        ];
                    }
                    $isSaved->categories()->insert($insertArray);
                }

                if (count($inputBusinessNatures)) {
                    $currentTime = Carbon::now();
                    $insertArray = [];
                    foreach ($inputBusinessNatures as $businessId) {
                        $insertArray[] = [
                            "pet_pro_id" => $isSaved->id,
                            "business_id" => $businessId,
                            "created_at" => $currentTime,
                            "updated_at" => $currentTime,
                        ];
                    }
                    $isSaved->business()->insert($insertArray);
                }
                if (count($inputPetType)) {
                    $currentTime = Carbon::now();
                    $insertArray = [];
                    foreach ($inputPetType as $petType) {
                        $insertArray[] = [
                            "pet_pro_id" => $isSaved->id,
                            "pet_type_id" => $petType,
                            "created_at" => $currentTime,
                            "updated_at" => $currentTime,
                        ];
                    }
                    $isSaved->petType()->insert($insertArray);
                }
                if ($isSaved->address_line_1 || $isSaved->address_line_2 || $isSaved->city_id || $isSaved->state_id || $isSaved->postal_code) {
                    $addressLatLong = GoogleMapHelper::getLatLongFromAddress($isSaved);
                    $isSaved->update($addressLatLong);
                }
                /*$googleData = GoogleMapHelper::getTimezone($isSaved);
                if( $googleData["timezone"] ) {
                $isSaved->timezone = Carbon::now()->timezone($googleData["timezone"])->format('P');
                $isSaved->save();
                } else {
                $isSaved->timezone = 'GMT-4';
                $isSaved->save();
                }*/

                $days = config('wagenabled.days');
                //if( $isSaved->timezone ) {
                foreach ($days as $day) {
                    $open_day = $day . "_open";
                    $close_day = $day . "_close";

                    if (isset($time_input[$open_day])) {
                        $time_input[$open_day] = Carbon::parse($time_input[$open_day]/*, $isSaved->timezone*/)->format('H:i:s');
                    } else {
                        $time_input[$open_day] = "";
                    }

                    if (isset($time_input[$close_day])) {
                        $time_input[$close_day] = Carbon::parse($time_input[$close_day]/*, $isSaved->timezone*/)->format('H:i:s');
                    } else {
                        $time_input[$close_day] = "";
                    }

                    PetProTimetable::create([
                        "pet_pro_id" => $isSaved->id,
                        "day" => $day,
                        "open" => $time_input[$open_day] ? $time_input[$open_day] : null,
                        "close" => $time_input[$close_day] ? $time_input[$close_day] : null,
                    ]);

                }
                //}

                $services = $request->get('services');
                if ($services) {
                    foreach ($services as $service) {
                        PetProServicesOffered::create([
                            "pet_pro_id" => $isSaved->id,
                            "service" => $service,
                        ]);
                    }
                }

                $galleryInput["pet_pro_id"] = $isSaved->id;
                if ($request->row) {
                    foreach ($request->row as $index => $row) {
                        if (isset($row["image"])) {
                            /*if(! $row["cropped_image"]) {
                            $isCreateThumb="0";
                            $galleryInput["is_cropped_image"] = 0;
                            } else {
                            $isCreateThumb="1";
                            $galleryInput["is_cropped_image"] = 1;
                            }*/
                            $galleryInput["is_cropped_image"] = 1;
                            $isCreateThumb = "1";
                            $imageStore = WagEnabledHelpers::saveUploadedImage($row["image"], config("wagenabled.path.doc.pet_pro_gallery_image_path"), "", $isCreateThumb, $height = 250, $width = 380, $row['cropped_image'], $isThumbOptimized = true);
                            if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                                $galleryInput["gallery_image"] = $imageStore['name'];
                            }
                            if ($request->get('is_cover_image')) {
                                if ($index == $request->get('is_cover_image')) {
                                    $galleryInput['is_cover_image'] = 1;
                                } else {
                                    $galleryInput['is_cover_image'] = 0;
                                }
                            }
                            $isSaved = PetProGallery::create($galleryInput);
                        }
                    }
                }
                return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.success"), 'Pet pro created');
                //return redirect($this->moduleRoute)->with("success", "Pet pro created");
            }
            return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.normal_error"), 'Sorry, Something went wrong please try again');
        } catch (\Exception $e) {
            return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.server_side"), $e->getMessage());
        }

    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $result = $this->model->with('timetable', 'images', 'categories')->find($id);

        if ($result) {
            $selectedCategories = $result->categories()->pluck("category_id", "category_id")->all();
            $categories = PetProCategory::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
            $countries = Country::pluck('name', 'id')->toArray();
            $selectedBusiness = $result->business()->pluck("business_id", "business_id")->all();
            $businessNatures = BusinessNature::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
            $selectedPetType = $result->petType()->pluck("pet_type_id", "pet_type_id")->all();
            $petType = PetType::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
            $states = State::where('country_id', 231)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
            if ($result->city_id) {
                $cities = City::where('state_id', $result->state_id)->where('is_valid', '=', 1)->pluck('name', 'id')->toArray();
            } else {
                $cities = [];
            }

            return view("$this->moduleView.edit", compact("result", "categories", "states", "cities", "selectedCategories", 'countries', 'businessNatures', 'selectedBusiness', 'petType', 'selectedPetType'));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro not found");
    }

    public function update(PetProRequest $request, $id)
    {

        $inputCategories = $request->get("category_id", []);
        $inputBusinessNatures = $request->get("business_id", []);
        $inputPetType = $request['pet_type_id'];
        $city = City::where('id', $request->get("city_id"))->first();

        try {
            $result = $this->model->find($id);
            if ($result) {

                $pet_pro_input = $request->only(['store_name', 'website_url', 'email', 'phone_number', 'address_line_1', 'address_line_2', 'city_id', 'state_id', 'postal_code', 'description', 'donation_link']);

                $time_input = $request->only(['monday_open', 'monday_close', 'tuesday_open', 'tuesday_close', 'wednesday_open', 'wednesday_close', 'thursday_open', 'thursday_close', 'friday_open', 'friday_close', 'saturday_open', 'saturday_close', 'sunday_open', 'sunday_close']);

                if ($request->get('is_featured_pet_pro')) {
                    $pet_pro_input['is_featured_pet_pro'] = 1;
                    $pet_pro_input['featured_title'] = $request->get('featured_title');
                    $pet_pro_input['featured_description'] = $request->get('featured_description');
                } else {
                    $pet_pro_input['is_featured_pet_pro'] = 0;
                    $pet_pro_input['featured_title'] = null;
                    $pet_pro_input['featured_description'] = null;
                }

                if ($request->get('is_featured_pet_pro')) {
                    $pet_pro_input['is_featured_pet_pro'] = 1;
                    $pet_pro_input['featured_title'] = $request->get('featured_title');
                    $pet_pro_input['featured_description'] = $request->get('featured_description');
                } else {
                    $pet_pro_input['is_featured_pet_pro'] = 0;
                    $pet_pro_input['featured_title'] = null;
                    $pet_pro_input['featured_description'] = null;
                }

                $isSaved = $result->update($pet_pro_input);
                if ($isSaved) {
                    $currentTime = Carbon::now();
                    if (count($inputCategories)) {
                        foreach ($inputCategories as $categoryId) {
                            $insertArray = [
                                "pet_pro_id" => $result->id,
                                "category_id" => $categoryId,
                            ];

                            $res = $result->categories()->updateOrCreate($insertArray, $insertArray);

                            $insertedCategories[] = $categoryId;
                        }
                    }

                    if (count($inputBusinessNatures)) {
                        foreach ($inputBusinessNatures as $businessId) {
                            $insertArray = [
                                "pet_pro_id" => $result->id,
                                "business_id" => $businessId,
                            ];

                            $res = $result->business()->updateOrCreate($insertArray, $insertArray);

                            $insertedBusiness[] = $businessId;
                        }
                    }
                    if (count($inputPetType)) {
                        foreach ($inputPetType as $PetTypeId) {
                            $insertArray = [
                                "pet_pro_id" => $result->id,
                                "business_id" => $PetTypeId,
                            ];

                            $res = $result->petType()->updateOrCreate($insertArray, $insertArray);

                            $insertedPetType[] = $PetTypeId;
                        }
                    }
                    $result->categories()->whereNotIn("category_id", $insertedCategories)->delete();
                    $result->business()->whereNotIn("business_id", $insertedBusiness)->delete();
                    $result->petType()->whereNotIn("business_id", $insertedPetType)->delete();

                    if ($city) {
                        $this->model->where('id', $id)->update([
                            "latitude" => $city->city_latitude,
                            "longitude" => $city->city_longitude,
                        ]);
                    }

                    if ($result->address_line_1 || $result->address_line_2 || $result->city_id || $result->state_id || $result->postal_code) {
                        $addressLatLong = GoogleMapHelper::getLatLongFromAddress($result);
                        $result->update($addressLatLong);
                    } elseif (!$city) {
                        $returnArr = [
                            "latitude" => null,
                            "longitude" => null,
                        ];
                        $result->update($returnArr);
                    }

                    /* $googleData = GoogleMapHelper::getTimezone($result);
                    if( $googleData["timezone"] ) {
                    $result->timezone = Carbon::now()->timezone($googleData["timezone"])->format('P');
                    $result->save();
                    }  else {
                    $result->timezone = 'GMT-4';
                    $result->save();
                    }*/

                    $days = config('wagenabled.days');
                    //if( $result->timezone ) {
                    foreach ($days as $day) {
                        $open_day = $day . "_open";
                        $close_day = $day . "_close";

                        if (isset($time_input[$open_day])) {
                            $time_input[$open_day] = Carbon::parse($time_input[$open_day]/*, $result->timezone*/)->format('H:i:s');
                        } else {
                            $time_input[$open_day] = "";
                        }

                        if (isset($time_input[$close_day])) {
                            $time_input[$close_day] = Carbon::parse($time_input[$close_day]/*, $result->timezone*/)->format('H:i:s');
                        } else {
                            $time_input[$close_day] = "";
                        }

                        $pet_pro_timetable = PetProTimetable::where("pet_pro_id", $result->id)
                            ->where("day", $day)
                            ->first();
                        if ($pet_pro_timetable) {
                            $pet_pro_timetable->open = $time_input[$open_day] ? $time_input[$open_day] : null;
                            $pet_pro_timetable->close = $time_input[$close_day] ? $time_input[$close_day] : null;
                            $pet_pro_timetable->save();
                        } else {
                            PetProTimetable::create([
                                "pet_pro_id" => $result->id,
                                "day" => $day,
                                "open" => $time_input[$open_day] ? $time_input[$open_day] : null,
                                "close" => $time_input[$close_day] ? $time_input[$close_day] : null,
                            ]);
                        }

                    }
                    //}

                    $services = $request->get('services');
                    if ($services) {
                        foreach ($services as $service) {
                            PetProServicesOffered::create([
                                "pet_pro_id" => $result->id,
                                "service" => $service,
                            ]);
                        }
                    }

                    $old_services = $request->get('old_services');
                    if ($old_services) {
                        foreach ($old_services as $old_service_id => $service_name) {
                            PetProServicesOffered::where('id', $old_service_id)->update([
                                "service" => $service_name,
                            ]);
                        }
                    }

                    $deletedServices = $request->get('deletedServices');
                    if ($deletedServices) {
                        $delete_services = explode(",", $deletedServices);
                        foreach ($delete_services as $id) {
                            PetProServicesOffered::where('id', $id)->delete();
                        }
                    }

                    $galleryInput["pet_pro_id"] = $result->id;
                    if ($request->row) {
                        foreach ($request->row as $index => $row) {
                            if (isset($row["image"])) {

                                /*if(! $row["cropped_image"]) {
                                $isCreateThumb="0";
                                $galleryInput["is_cropped_image"] = 0;
                                } else {
                                $isCreateThumb="1";
                                $galleryInput["is_cropped_image"] = 1;
                                }*/
                                $isCreateThumb = "1";
                                $galleryInput["is_cropped_image"] = 1;
                                $imageStore = WagEnabledHelpers::saveUploadedImage($row["image"], config("wagenabled.path.doc.pet_pro_gallery_image_path"), "", $isCreateThumb, $height = 250, $width = 380, $row['cropped_image'], $isThumbOptimized = true);
                                if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                                    $galleryInput["gallery_image"] = $imageStore['name'];
                                }
                                if ($request->get('is_cover_image')) {
                                    if ($index == $request->get('is_cover_image')) {
                                        $galleryInput['is_cover_image'] = 1;
                                        PetProGallery::where('pet_pro_id', $result->id)->update(['is_cover_image' => 0]);
                                    } else {
                                        $galleryInput['is_cover_image'] = 0;

                                    }
                                }
                                PetProGallery::create($galleryInput);
                            }
                        }
                    }

                    if ($request->old_row) {
                        foreach ($request->old_row as $index => $row) {
                            $galleryResult = PetProGallery::find($index);

                            if (isset($row["image"])) {
                                /*if(! $row["cropped_image"]) {
                                $isCreateThumb="0";
                                $updateGalleryInput["is_cropped_image"] = 0;
                                } else {
                                $isCreateThumb="1";
                                $updateGalleryInput["is_cropped_image"] = 1;
                                }*/
                                $isCreateThumb = "1";
                                $galleryInput["is_cropped_image"] = 1;
                                $imageStore = WagEnabledHelpers::saveUploadedImage($row["image"], config("wagenabled.path.doc.pet_pro_gallery_image_path"), $galleryResult->gallery_image, $isCreateThumb, $height = 250, $width = 380, $row['cropped_image'], $isThumbOptimized = true);
                                if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                                    $updateGalleryInput["gallery_image"] = $imageStore['name'];
                                }
                            }

                            if ($request->get('is_cover_image')) {
                                if (strpos($request->get('is_cover_image'), 'old_') !== false) {
                                    if ($index == str_replace('old_', '', $request->get('is_cover_image'))) {
                                        $updateGalleryInput['is_cover_image'] = 1;
                                        PetProGallery::where('pet_pro_id', $result->id)->where('id', '!=', $galleryResult->id)->update(['is_cover_image' => 0]);
                                    }
                                } else {
                                    $updateGalleryInput['is_cover_image'] = 0;
                                }
                            }
                            if (isset($updateGalleryInput)) {
                                $galleryResult->update($updateGalleryInput);
                            }
                            $updateGalleryInput = [];
                        }
                    }

                    $deletedGallery = $request->get('deletedGallery');
                    if ($deletedGallery) {
                        $delete_gallery = explode(",", $deletedGallery);
                        foreach ($delete_gallery as $id) {
                            $galleryResult = PetProGallery::find($id);
                            $fileOldName = $galleryResult->gallery_image;
                            $galleryResult->delete();
                            if ($fileOldName) {
                                $deleteFileList = array();
                                $deleteFileList[] = config("wagenabled.path.doc.pet_pro_gallery_image_path") . $fileOldName;
                                $deleteFileList[] = config("wagenabled.path.doc.pet_pro_gallery_image_path") . 'thumb/' . $fileOldName;
                                WagEnabledHelpers::deleteIfFileExist($deleteFileList);
                            }
                        }
                    }
                    return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.success"), 'Pet pro updated');
                    //return redirect($this->moduleRoute)->with("success", "Pet pro updated");
                }
            }

            return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.normal_error"), 'Sorry, Something went wrong please try again');
        } catch (\Exception $e) {
            return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.server_side"), $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $result = array();
        $data = $this->model->find($id);
        if ($data) {
            $res = $data->delete();
            if ($res) {
                $result['message'] = "Pet pro deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting pet pro";
                $result['code'] = 400;
            }
        } else {
            $result['message'] = "Pet pro not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
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

    public function getGeocodeData(Request $request)
    {
        $code = config("wagenabled.status_codes.normal_error");
        $message = "";
        $data = [];
        $responseData = [];
        try {
            if ($request->get('postal_code', '')) {
                $isExistingCityGetCount = City::where('zipcode', trim($request->get('postal_code')))->where('is_valid', 1)->count();
                if ($isExistingCityGetCount != 1) {
                    $data[] = $request->get('postal_code', '');
                    $GooogleResponseData = GoogleMapHelper::getGeocodeData($data);

                    if (count($GooogleResponseData) > 0) {
                        $responseData = $GooogleResponseData;
                        $state_name = $GooogleResponseData["state"];
                        $city_name = $GooogleResponseData["city"];
                        $zipcode = $request->get('postal_code', '');
                        $latitude = $GooogleResponseData["latitude"];
                        $longitude = $GooogleResponseData["longitude"];

                        if ($city_name) {
                            $isExistingCity = City::where('name', 'like', $city_name);
                            $cities = clone $isExistingCity;
                            $isExistingCity = $isExistingCity->first();
                            if ($isExistingCity) {

                                $responseData["state_id"] = $isExistingCity->state_id;
                                $responseData["city_id"] = $isExistingCity->id;

                                $isInvalidCity = $cities->where('is_valid', '0')->first();
                                if ($isInvalidCity) {
                                    $cities = City::where('name', 'like', $city_name)->update([
                                        'is_valid' => 1,
                                    ]);
                                }
                            } else {
                                if ($state_name) {
                                    $state = State::where('name', 'like', $state_name)->first();
                                    if (!$state) {
                                        $country = Country::where('name', 'United States')->first();
                                        if ($country) {
                                            $state = State::create([
                                                "country_id" => $country->id,
                                                "name" => $state_name,
                                            ]);
                                        }
                                    }
                                    if ($state) {
                                        $isSavedCity = City::create([
                                            'name' => $city_name,
                                            'state_id' => $state->id,
                                            'zipcode' => $zipcode,
                                            'city_latitude' => $latitude,
                                            'city_longitude' => $longitude,
                                            'is_valid' => 1,
                                        ]);

                                        $responseData["city_id"] = $isSavedCity->id;
                                        $responseData["state_id"] = $state->state_id;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $isExistingCityGet = City::where('zipcode', $request->get('postal_code'))->where('is_valid', 1)->orderBy('name')->orderBy('id')->first();
                    $responseData["city_id"] = $isExistingCityGet->id;
                    $responseData["state_id"] = $isExistingCityGet->state_id;
                }
                $code = config("wagenabled.status_codes.success");
            }
        } catch (Exception $e) {
            $message = "Please, try again!";
        }

        return WagEnabledHelpers::apiJsonResponse($responseData, $code, $message);
    }

}
