<?php
namespace App\Http;

use App\Mail\Admin\OffersNotificationsMail;
use App\Mail\Admin\VerifyBalanceReportedAmountMail;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use FFMPEG;
use File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Request;
use Response;

class WagEnabledHelpers
{   
    public static function saveUploadedImage($file, $storagePath, $fileOldName='', $isCreateThumb="1", $height=200, $width=200, $cropped_image='', $isThumbOptimized = false) {        
        $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 5);

        $timestamp = Carbon::createFromFormat('U.u', microtime(true))->format("YmdHisu");
        $nameWithoutExtension = preg_replace("/[^a-zA-Z0-9]/", "", '') . '' . $timestamp;
        $nameWithoutExtension = $nameWithoutExtension.'-'.$randomString;
        $fileExtension = $file->getClientOriginalExtension();
        $name = $nameWithoutExtension. '.' . $fileExtension;
        $name = str_replace([' ', ':', '-'], "", $name);

        $deleteFileList = array();
        $thumbName = "";

        try {
            $img = Image::make($file->getRealPath())
                                ->orientate()
                                ->encode($fileExtension);
                
            $media_file_upload_res = Storage::put($storagePath . $name, $img);
            if($media_file_upload_res) {
                $imageName = $name;
                $thumbnailStoragePath = $storagePath."thumb/";
                $smallThumbnailStoragePath = $storagePath."small-thumb/";
                if($isCreateThumb == "1") {
                    if ($cropped_image != "") {
                        $image_thumb_content = Image::make($cropped_image)
                                            ->orientate()
                                            ->encode($fileExtension, 50);                                        
                        Storage::put($thumbnailStoragePath . $name, $image_thumb_content);

                        if( $isThumbOptimized ) {                                                                           
                            $image_small_thumb_content = Image::make($cropped_image)
                                    ->resize(500, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    })
                                    ->orientate()
                                    ->encode($fileExtension); 
                            Storage::put($smallThumbnailStoragePath . $name, $image_small_thumb_content); 

                        }
                        /* Resize profile picture small */
                       /* $image_thumb_content = Image::make($cropped_image)
                                ->resize($width, $height, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                ->orientate()
                                ->encode($fileExtension);                    
                        Storage::put($thumbnailStoragePath . $name, $image_thumb_content);*/
                    } else{      
                        if( $isThumbOptimized ) {
                            $original_image_contents = Storage::get($storagePath . $name);                            
                            /* Resize profile picture small */
                            $image_thumb_content = Image::make($original_image_contents)
                                                        ->orientate()
                                                        ->encode($fileExtension, 50);  
                            Storage::put($thumbnailStoragePath . $name, $image_thumb_content);  

                            $image_small_thumb_content = Image::make($original_image_contents)
                                    ->resize(500, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    })
                                    ->orientate()
                                    ->encode($fileExtension); 
                            Storage::put($smallThumbnailStoragePath . $name, $image_small_thumb_content); 

                        } else {

                            $original_image_contents = Storage::get($storagePath . $name);
                            
                            /* Resize profile picture small */
                            $image_thumb_content = Image::make($original_image_contents)
                                    ->resize($width, $height, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    })
                                    ->orientate()
                                    ->encode($fileExtension);                    
                            Storage::put($thumbnailStoragePath . $name, $image_thumb_content);
                        }              
                    }
                }

                
            }

            if (!empty($fileOldName)) {
                $deleteFileList[] = $storagePath.$fileOldName;
                $deleteFileList[] = $storagePath."thumb/".$fileOldName;
            }

            if(count($deleteFileList) > 0) {
                WagEnabledHelpers::deleteIfFileExist($deleteFileList);
            }

            $returnArray = array('name' => $imageName, "error_msg" => "");
            return $returnArray;

        } catch (Exception $e) {
            WagEnabledHelpers::deleteIfFileExist($deleteFileList);
            $returnArray = array('name' => "", "error_msg" => $e->getMessage());
            return $returnArray;
        }
    }


    public static function saveUploadedImageS3($file, $storagePath, $fileOldName='', $isCreateThumb="1", $height=200, $width=200) {
        $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 5);

        $timestamp = Carbon::createFromFormat('U.u', microtime(true))->format("YmdHisu");
        $nameWithoutExtension = preg_replace("/[^a-zA-Z0-9]/", "", '') . '' . $timestamp;
        $nameWithoutExtension = $nameWithoutExtension.'-'.$randomString;
        $fileExtension = $file->getClientOriginalExtension();
        $name = $nameWithoutExtension. '.' . $fileExtension;
        $name = str_replace([' ', ':', '-'], "", $name);

        $deleteFileList = array();
        $thumbName = "";

        try {
            $img = Image::make($file->getRealPath())
                                ->orientate()
                                ->encode($fileExtension);
                
            $media_file_upload_res = Storage::disk('s3')->put($storagePath . $name, $img);
            if($media_file_upload_res) {
                $imageName = $name;
                if($isCreateThumb == "1") {
                    $original_image_contents = Storage::disk('s3')->get($storagePath . $name);
                    
                    /* Resize profile picture small */
                    $image_thumb_content = Image::make($original_image_contents)
                            ->resize($width, $height, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->orientate()
                            ->encode($fileExtension);
                    
                    $thumbnailStoragePath = $storagePath."thumb/";
                    Storage::disk('s3')->put($thumbnailStoragePath . $name, $image_thumb_content);
                }

                
            }

            if (!empty($fileOldName)) {
                $deleteFileList[] = $storagePath.$fileOldName;
                $deleteFileList[] = $storagePath."thumb/".$fileOldName;
            }

            if(count($deleteFileList) > 0) {
                WagEnabledHelpers::deleteIfFileExistS3($deleteFileList);
            }

            $returnArray = array('name' => $imageName, "error_msg" => "");
            return $returnArray;

        } catch (Exception $e) {
            WagEnabledHelpers::deleteIfFileExistS3($deleteFileList);
            $returnArray = array('name' => "", "error_msg" => $e->getMessage());
            return $returnArray;
        }
    }

    public static function deleteIfFileExist($files){
        if(is_array($files) && count($files)>0) {
            foreach ($files as $key => $path) {
                if (!empty($path) && Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        }
        else {
            if (!empty($files) && Storage::exists($files)) {
                Storage::delete($files);
            }
        }
    }

    public static function deleteIfFileExistS3($files){
        if(is_array($files) && count($files)>0) {
            foreach ($files as $key => $path) {
                if (!empty($path) && Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
            }
        }
        else {
            if (!empty($files) && Storage::disk('s3')->exists($files)) {
                Storage::disk('s3')->delete($files);
            }
        }
    }
    
    public static function uploadFile($file, $storagePath, $fileOldName = '') {

        $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 5);

        $timestamp = Carbon::createFromFormat('U.u', microtime(true))->format("YmdHisu");
        $nameWithoutExtension = preg_replace("/[^a-zA-Z0-9]/", "", '') . '' . $timestamp;
        $nameWithoutExtension = $nameWithoutExtension.'-'.$randomString;
        $fileExtension = $file->getClientOriginalExtension();
        $name = $nameWithoutExtension. '.' . $fileExtension;
        $name = str_replace([' ', ':', '-'], "", $name);

        $deleteFileList = array();

        try {
            $returnArray = array('name' => "", "error_msg" => "Sorry something went wrong please try again");
            
            if( Storage::put($storagePath . $name,  file_get_contents($file->getRealPath())) ) {
                $returnArray = array('name' => $name, "error_msg" => "");
            }

            if (!empty($fileOldName)) {
                $deleteFileList[] = $storagePath.$fileOldName;
            }

            if(count($deleteFileList) > 0) {
                WagEnabledHelpers::deleteIfFileExist($deleteFileList);
            }

            return $returnArray;

        } catch (Exception $e) {
            $returnArray = array('name' => "", "error_msg" => $e.getMessage());
            return $returnArray;
        }
    }
    public static function apiUserNotFoundResponse()
    {
        $statusCodes = config("wagenabled.status_codes");
        return WagEnabledHelpers::apiJsonResponse([], $statusCodes['auth_fail'], "User not found");
    }

    public static function apiJsonResponse($responseData=[], $code='', $message = "")
    {
        $statusCodes = config("wagenabled.status_codes");
        if($code == '') {
            $code = $statusCodes['success'];
            if(count($responseData) == 0) {
                $code = $statusCodes['success_with_empty'];
            }
        }

        $data = array(
                        'message' => $message,
                        'data' => $responseData
                    );
        return Response::json($data, $code);
    }

    public static function generateUniqueFileName($fileExtension)
    {
        $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 5);
        $timestamp = Carbon::createFromFormat('U.u', microtime(true))->format("YmdHisu");
        $nameWithoutExtension = preg_replace("/[^a-zA-Z0-9]/", "", '') . '' . $timestamp;
        $nameWithoutExtension = $nameWithoutExtension.'-'.$randomString;                            
        $name = $nameWithoutExtension. '.' . $fileExtension;
        $name = str_replace([' ', ':', '-'], "", $name);
        return $name;
    }

    public static function apiValidationFailResponse($validator)
    {   
        $statusCodes = config("wagenabled.status_codes");
        $messages = $validator->errors();
        if (is_object($messages)) {
            $messages = $messages->toArray();
        }            
        return WagEnabledHelpers::apiJsonResponse($messages, $statusCodes['form_validation'], "Validation Error");
    }

    public static function generateUniqueSlug($model, $stringForSlug, $id = 0, $slugColumn = "slug")
    {
        $slug = Str::slug($stringForSlug);
        
        $allSlugs = WagEnabledHelpers::getRelatedSlugs($slug, $model, $id, $slugColumn);

        if (! $allSlugs->contains($slugColumn, $slug)){
            return $slug;
        }

        $i = 1;
        do {
            $newSlug = $slug.'-'.$i;
            $i++;
        } while ($allSlugs->contains($slugColumn, $newSlug));

        $slug = $newSlug;

        $allSlugs = WagEnabledHelpers::getRelatedSlugs($slug, $model, $id, $slugColumn);
        if (! $allSlugs->contains($slugColumn, $slug)){
            return $slug;
        }

        return WagEnabledHelpers::generateUniqueSlug($model, $slug, $id, $slugColumn);
    }

    public static function getRelatedSlugs($slug, $model, $id, $slugColumn)
    {
        return $model->select($slugColumn)->where($slugColumn, 'like', $slug.'%')
                                    ->where('id', '<>', $id)
                                    ->withTrashed()
                                    ->get();
    }

    public static function buildTreeStructure(array $elements, $parentId = '0', $idColumnName = "id", $parentIdColumnName = "parent_comment_id") {
        $branch = array();
        if (count($elements) > 0) {
            foreach ($elements as $element) {
                if ($element[$parentIdColumnName] == $parentId) {
                    $children = self::buildTreeStructure($elements, $element[$idColumnName], $idColumnName, $parentIdColumnName);
                    if ($children) {                          
                         $element['children'] = $children;
                    }
                    $branch[] = $element;
                }
            }
        }            
        return $branch;
    }

    public static function getYouTubeVideoID($url) {
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $params);
        if (isset($params['v']) && strlen($params['v']) > 0) {
            return $params['v'];
        } else {
            return "";
        }
    }

    public static function ISO8601ToSeconds($ISO8601){

        $start = new \DateTime('@0'); // Unix epoch
        $start->add(new \DateInterval($ISO8601));
        return $start->format('H:i:s.v');
    }

    public static function getYouTubeVideoDuration($url) {
        try {
            $youtubeID = str_replace('https://www.youtube.com/embed/', '', $url);             
            if( $youtubeID ) {
                $api_key = env('GOOGLE_API_KEY', 'AIzaSyDfSFDURSYNLP98jkn2pIurVv3QThXNg2Q');
                $api_url = 'https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id='.$youtubeID.'&key=' . $api_key;
                $data = json_decode(@file_get_contents($api_url));  
                if( $data ) {
                    if( isset($data->items[0]) && isset($data->items[0]->contentDetails) && isset($data->items[0]->contentDetails->duration) ) {
                        return WagEnabledHelpers::ISO8601ToSeconds($data->items[0]->contentDetails->duration);
                    }
                }
            }
            return '';   
        } catch (Exception $e) {
            $returnArray = array('name' => "", "error_msg" => $e.getMessage());
            return $returnArray;
        }

    }
}
