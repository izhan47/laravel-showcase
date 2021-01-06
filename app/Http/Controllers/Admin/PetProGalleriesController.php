<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PetProGalleryRequest;
use App\Http\WagEnabledHelpers;
use App\Models\PetPro;
use App\Models\PetProGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class PetProGalleriesController extends Controller
{
    public function __construct(PetProGallery $model)
    {     
        set_time_limit(0);       
        $this->moduleName = "Pet Pro Galleries";
        $this->singularModuleName = "Pet Pro Gallery";
        $this->moduleView = "admin.main.pet-pro-gallery";
        $this->moduleRoute = url('admin/pet-pros');
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('moduleView', $this->moduleView);
    }

    public function index()
    {

    }

    public function getDatatable(Request $request, $pet_pro_id)
    {
    
    }
    
    public function create($pet_pro_id)
    {
        $petPro = PetPro::find($pet_pro_id);
        if( $petPro ) {
            $moduleRoute = url('admin/pet-pros/'.$pet_pro_id.'/gallery');
            View::share('module_route', $moduleRoute);
            return view("admin.main.pet-pro-gallery.create", compact('pet_pro_id'));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");
    }
 
    public function store(PetProGalleryRequest $request, $pet_pro_id)
    {   
        $input = [];
        try {    
            $petPro = PetPro::find($pet_pro_id);
            if( $petPro ) {
                
                $input["pet_pro_id"] = $pet_pro_id;

                foreach ($request->row as $index => $row) {
                    if( isset($row["image"]) ) {       
                        if(! $row['cropped_image'] ) {
                            $isCreateThumb="0";
                            $input["is_cropped_image"] = 0;
                        } else {
                            $isCreateThumb="1";
                            $input["is_cropped_image"] = 1;
                        }
                        $imageStore = WagEnabledHelpers::saveUploadedImage($row["image"], config("wagenabled.path.doc.pet_pro_gallery_image_path"), "", $isCreateThumb, $height=250, $width=250, $row['cropped_image']);            
                        if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                            $input["gallery_image"] = $imageStore['name'];                    
                        }                                          
                        if( $request->get('is_cover_image') ){
                            if( $index == $request->get('is_cover_image') ) {
                                $input['is_cover_image'] = 1;
                                PetProGallery::where('pet_pro_id', $pet_pro_id)->update(['is_cover_image' => 0]); 
                            }
                        }
                        $isSaved = $this->model->create($input);                    
                    }   
                }   

                return WagEnabledHelpers::apiJsonResponse([] , config("wagenabled.status_codes.success"), 'File Uploaded Successfully');                    
                //return redirect($this->moduleRoute.'/'.$pet_pro_id.'/edit')->with("success", "Pet pro gallery created");
                        
            }
            return WagEnabledHelpers::apiJsonResponse([] , config("wagenabled.status_codes.normal_error"), 'Sorry, Something went wrong please try again');                    
            //return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {
            return WagEnabledHelpers::apiJsonResponse([] , config("wagenabled.status_codes.normal_error"), $e->getMessage());                    
            //return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }

    }

    public function edit($pet_pro_id, $id)
    {
        $petPro = PetPro::find($pet_pro_id);
        if( $petPro ) {
            $moduleRoute = url('admin/pet-pros/'.$pet_pro_id.'/gallery');
            View::share('module_route', $moduleRoute);
            $result = $this->model->find($id);
            if ($result) {
                return view("admin.main.pet-pro-gallery.edit", compact("result", "pet_pro_id"));
            }
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro gallery not found");
    }
   
    public function update(PetProGalleryRequest $request, $pet_pro_id, $id)
    {         
        $input = [];       
        try {
            $petPro = PetPro::find($pet_pro_id);
            if( $petPro ) {
                $result = $this->model->find($id);            
                if ($result) {  

                    if ($request->file('image', false)) {    
                        if(! $request->get('cropped_image')) {
                            $isCreateThumb="0";
                            $input["is_cropped_image"] = 0;
                        } else {
                            $isCreateThumb="1";
                            $input["is_cropped_image"] = 1;
                        }    
                        $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.pet_pro_gallery_image_path"), $result->gallery_image, $isCreateThumb, $height=250, $width=250, $request->get('cropped_image'));            
                        if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                            $input["gallery_image"] = $imageStore['name'];                    
                        }                    
                    } 

                    if( $request->get('is_cover_image') ) {
                        $input['is_cover_image'] = 1;                        
                        PetProGallery::where('pet_pro_id', $pet_pro_id)->where('id', "!=", $result->id )->update(['is_cover_image' => 0]);
                    } else {                       
                        $input['is_cover_image'] = 0;                        
                    }

                    $isSaved = $result->update($input);        
                    if ($isSaved) {
                        return WagEnabledHelpers::apiJsonResponse([] , config("wagenabled.status_codes.success"), 'Pet pro gallery updated');   
                        //return redirect($this->moduleRoute.'/'.$pet_pro_id.'/edit')->with("success", "Pet pro gallery updated");
                    }
                }
            }
            return WagEnabledHelpers::apiJsonResponse([] , config("wagenabled.status_codes.normal_error"), 'Sorry, Something went wrong please try again'); 

        } catch (\Exception $e) {            
            return WagEnabledHelpers::apiJsonResponse([] , config("wagenabled.status_codes.server_side"), $e->getMessage());
        }
    }

    public function destroy($pet_pro_id, $id)
    {
        $result = array();
        $petPro = PetPro::find($pet_pro_id);     

        if ($petPro) {            
            $data = $this->model->find($id);
            if ($data) { 

                $fileOldName = $data->gallery_image;        
                $res = $data->delete();
                
                if( $fileOldName ) {
                    // delete old file 
                    $deleteFileList = array();
                    $deleteFileList[] =  config("wagenabled.path.doc.pet_pro_gallery_image_path").$fileOldName;
                    $deleteFileList[] =  config("wagenabled.path.doc.pet_pro_gallery_image_path").'thumb/'.$fileOldName;
                    WagEnabledHelpers::deleteIfFileExist($deleteFileList);
                }
                if ($res) {
                    $result['message'] =  "Pet pro gallery deleted.";
                    $result['code'] = 200;
                } else {
                    $result['message'] = "Error while deleting pet pro gallery";
                    $result['code'] = 400;
                }
            } else {
                $result['message'] = "Pet pro gallery not Found!";
                $result['code'] = 400;
            }                                
        } else {
            $result['message'] = "Sorry, Something went wrong please try again!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
