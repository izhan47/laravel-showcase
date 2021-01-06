<?php

namespace App\Library;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Response;
use Storage;
use App\Models\City;
use App\Models\State;
use App\Models\Country;

class GoogleMapHelper {

    public static function getLatLongFromAddress($addressObj) {
        $googleMapApiKey = config("wagenabled.google_map.api_key");

        $returnArr = [
            "latitude" => null,
            "longitude" => null,
        ];

        if($addressObj) {
            $addressArr = [];
            /*if(!empty($addressObj->store_name)) {
                $addressArr[] = $addressObj->store_name;
            }*/
            
            if(!empty($addressObj->address_line_1) ) {
                $addressArr[] = $addressObj->address_line_1;
            }

            if(!empty($addressObj->address_line_2) ) {
                $addressArr[] = $addressObj->address_line_2;
            }

            if(!empty($addressObj->city_id)) {
                $city = City::find($addressObj->city_id);
                if($city) {
                    $addressArr[] = $city->name;
                }
            }
            if(!empty($addressObj->state_id)) {
                $state = State::find($addressObj->state_id);
                if($state) {
                    $addressArr[] = $state->name;
                }
            }
            $addressArr[] = 'USA';
            if(!empty($addressObj->postal_code)) {
                $addressArr[] = $addressObj->postal_code;
            }
            
            $addressStr = implode(", ", $addressArr);
            
            if(!empty($addressStr)) {                
                // Get JSON results from this request
                $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($addressStr).'&key='.$googleMapApiKey);             
                $geo = json_decode($geo, true); // Convert the JSON to an array              
                if (isset($geo['status']) && ($geo['status'] == 'OK')) {
                    $returnArr["latitude"] = $geo['results'][0]['geometry']['location']['lat']; // Latitude
                    $returnArr["longitude"] = $geo['results'][0]['geometry']['location']['lng']; // Longitude
                } else {
                    \Log::info($geo);
                }
            }
        }

        return $returnArr;
    }
        
    public static function getTimezone($addressObj) {
        $googleMapApiKey = config("wagenabled.google_map.api_key");
       
        $returnArr = [
            "timezone" => "",
        ];

        if($addressObj) {
            $addressArr = [];
            if($addressObj->latitude && $addressObj->longitude ) {      
                $timestamp = Carbon::now()->timestamp;  
                $geo = file_get_contents('https://maps.googleapis.com/maps/api/timezone/json?location='.$addressObj["latitude"].','.$addressObj["longitude"].'&timestamp='.$timestamp.'&key='.$googleMapApiKey); 
                $geo = json_decode($geo, true);   
                $returnArr["timezone"] = $geo["timeZoneId"];

           }
        }

        return $returnArr;
    }

    public static function getGeocodeData($addressObj) {
        $googleMapApiKey = config("wagenabled.google_map.api_key");

        $returnArr = [];

        if($addressObj) {                        
            $addressStr = implode(", ", $addressObj);
            
            if(!empty($addressStr)) {                
                // Get JSON results from this request
                $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($addressStr).'&key='.$googleMapApiKey);             
                $geo = json_decode($geo, true); // Convert the JSON to an array        
                $city = "";
                $state = "";   
                $temp_city_name = "";   
                if (isset($geo['status']) && ($geo['status'] == 'OK')) {
                    if (count($geo['results']) > 0) {
                        //break up the components
                        $arrComponents = $geo['results'][0]['address_components'];

                        foreach($arrComponents as $index=>$component) {
                            $type = $component['types'][0];

                            if ($temp_city_name == "" && ($type == "administrative_area_level_2") ) {
                                $temp_city_name = trim($component['long_name']);
                            }

                            if ($city == "" && ($type == "sublocality_level_1" || $type == "locality") ) {
                                $city = trim($component['long_name']);
                            }
                            if ($state == "" && $type=="administrative_area_level_1") {
                                $state = trim($component['long_name']);
                            }                            
                            if ($city != "" && $state != "") {
                                break;
                            }
                        }
                        if( $city == "" ) {
                            $city = $temp_city_name;
                        }
                    }

                    $returnArr["latitude"] = $geo['results'][0]['geometry']['location']['lat']; // Latitude
                    $returnArr["longitude"] = $geo['results'][0]['geometry']['location']['lng']; // Longitude
                    $returnArr["city"] = $city; 
                    $returnArr["state"] = $state; 
                } else {
                    \Log::info($geo);
                }
            }
        }

        return $returnArr;
    }

}
