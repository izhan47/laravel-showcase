<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WatchAndLearnRequest;
use App\Http\WagEnabledHelpers;
use App\Models\WatchAndLearn;
use App\Models\WatchAndLearnAuthor;
use App\Models\WatchAndLearnCategory;
use App\Models\WatchAndLearnMedias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Storage;
use Yajra\Datatables\Datatables;
use App\Models\WatchAndLearnComment;

class WatchAndLearnController extends Controller
{
    public function __construct(WatchAndLearn $model)
    {        
        $this->moduleName = "Watch And Learn";
        $this->singularModuleName = "Watch And Learn";
        $this->moduleRoute = url('admin/watch-and-learn');
        $this->moduleView = "admin.main.watch-and-learn";
        $this->model = $model;
		$this->statusCodes = config("wagenabled.status_codes");

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('module_route', $this->moduleRoute);
        View::share('moduleView', $this->moduleView);
    }

    public function index()
    {      
        $frontUrl = env('REACT_SERVER_BASE_URL');
		view()->share('isIndexPage', true);
		
		$categories = WatchAndLearnCategory::GetWatchAndLearnCategory()->pluck('name', 'id')->toArray();

        return view("$this->moduleView.index", compact('frontUrl', 'categories'));
    }

    public function getDatatable(Request $request)
    {
        $blogMode = $request->get('blogMode');
		$category_id = $request->get('category_id');

        $result = $this->model->with('category', 'author')->GetWatchAndLearnCategory()->select("*")->where('status', $blogMode)->orderBy('id', 'desc');

		if($category_id != ""){
            $result = $result->where('category_id','=',$category_id);
		}

        return Datatables::of($result)
        ->addColumn('formated_author', function ($result) {
            if( $result->author  ) {
                return $result->author->name;
            }
            return '-';            
        })
        ->addIndexColumn()
        ->make(true);        
    }
    
    public function create()
    {
        $categories = WatchAndLearnCategory::GetWatchAndLearnCategory()->orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        $authors = WatchAndLearnAuthor::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        return view("$this->moduleView.create", compact('categories', 'authors'));
    }
 
    public function store(WatchAndLearnRequest $request)
    {
        $input = $request->except(['_token', 'image','cropped_image', 'video_file', 'blogMode']);
        try {   
            if ($request->file('image', false)) {        
                $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.watch_and_learn_thumbnail_path"), "", $isCreateThumb="1", $height=250, $width=380, $request->get('cropped_image'));            
                if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                    $input["thumbnail"] = $imageStore['name'];                    
                }                    
            } 

            // if ($request->get('video_type') == 'video_upload') {                       
            //     if ($request->file('video_file', false)) {        
            //         $imageStore = WagEnabledHelpers::uploadFile($request->file('video_file'), config("wagenabled.path.doc.watch_and_learn_video_path"), "");            
            //         if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
            //             $input["video_file"] = $imageStore['name']; 
            //             $input["embed_link"] = null; 

            //             $video_file = $request->file('video_file');
            //             $input['duration'] =  exec("ffmpeg -i $video_file 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");                   
            //         }                    
            //     }  
            // } else {
            //     //$file_path='https://www.youtube.com/watch?v=uilkmUoXoLU';
            //     if($input['embed_link']) {
            //         $input['duration'] = WagEnabledHelpers::getYouTubeVideoDuration($input['embed_link']);
            //     }
            // }
            $isSaved = $this->model->create($input);
            if ($isSaved) {
                if( $request->get('blogMode') == 'draft' ) {
                    return redirect($this->moduleRoute)->with("success", "Watch and learn created");
                }
                $viewData['watchAndLearn'] = $isSaved;                       
                return view("$this->moduleView.content-builder", $viewData);
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }

    }

    public function show($id)
    {    
        $result = $this->model->withCount(['comments' => function($query){
			$query->where('parent_comment_id', 0);
		}])
		->find($id);        
		$back_url_path = $this->moduleRoute;

		if ($result) {
            return view("$this->moduleView.show", compact("result", 'back_url_path'));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Watch and learn not found");            
    }
    
    public function edit($id)
    {
        $result = $this->model->find($id);
        $categories = WatchAndLearnCategory::GetWatchAndLearnCategory()->orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        $authors = WatchAndLearnAuthor::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        if ($result) {
            return view("$this->moduleView.edit", compact("result","categories", 'authors'));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Watch and learn not found");
    }    

    public function update(WatchAndLearnRequest $request, $id)
    {               
        $input = $request->except(['_token', 'image','cropped_image', 'video', 'blogMode']);      
        try {
            $result = $this->model->find($id);            
            if ($result) {   
                $old_file_name = '';
                if ($request->file('image', false)) {        
                    $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.watch_and_learn_thumbnail_path"), $result->thumbnail, $isCreateThumb="1", $height=250, $width=380, $request->get('cropped_image'));            
                    if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                        $input["thumbnail"] = $imageStore['name'];                    
                    }                    
                } 

                // if ($request->get('video_type') == 'video_upload') {        
                //     if ($request->file('video_file', false)) {        
                //         $imageStore = WagEnabledHelpers::uploadFile($request->file('video_file'), config("wagenabled.path.doc.watch_and_learn_video_path"), $result->video_file);            
                //         if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                //             $input["video_file"] = $imageStore['name']; 
                //             $input["embed_link"] = null; 
                //             $video_file = $request->file('video_file');
                //             $input['duration'] =  exec("ffmpeg -i $video_file 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//"); 

                //         }                    
                //     }  
                // } else {
                //     $input["video_file"] = null;  
                //     if( $input['embed_link'] ) {                    
                //         if( $input['embed_link'] !=  $result->embed_link) {
                //             $input['duration'] = WagEnabledHelpers::getYouTubeVideoDuration($input['embed_link']);  
                //         }
                //     } else {
                //         $input['duration'] = null;
                //     }
                // }

                // if( $result->video_type == 'video_upload' ) {
                //     $old_file_name = $result->video_file;
                // }

                if( $request->get('blogMode') == 'draft' ) {                        
                    $input["status"] = 'draft';  
                }
                $isSaved = $result->update($input);  

                if ($isSaved) {
                    
                    if( $old_file_name ) {
                        $deleteFileList[] =  config("wagenabled.path.doc.watch_and_learn_video_path").$old_file_name;
                        WagEnabledHelpers::deleteIfFileExist($deleteFileList);  
                    }

                    if( $request->get('blogMode') == 'draft' ) {                        
                        return redirect($this->moduleRoute);
                    } 
                    if( $request->get('blogMode') == 'publish'  ) {
                        return redirect($this->moduleRoute);                        
                    }            

                    $viewData['watchAndLearn'] = $result;                      
                    return view("$this->moduleView.content-builder", $viewData);

                    return redirect($this->moduleRoute)->with("success", "Watch and learn updated");
                }
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {            
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }

    public function buildWithContentBuilder(Request $request, $id = null)
    {
        $viewData = [];
        $watchAndLearn = $this->model::whereId($id)->first();
        if ($watchAndLearn) {
            $viewData['watchAndLearn'] = $watchAndLearn;
        }

        return view("$this->moduleView.content-builder", $viewData);
    }

    public function setDescriptionByContentBuilder(Request $request, $id)
    {
        try {
            $data = $this->model::whereId($id)->first();

            if ($data) {
                $input = $request->only(['description']);

                $status = $data->update($input);

                if ($status) {
                    $result = $this->model->find($id);                    
                    $back_url_path = $this->moduleRoute.'/'.$id.'/edit/buildwithcontentbuilder';
                    if ($result) {
                        return view("$this->moduleView.show", compact("result", 'back_url_path'));
                    }
                }
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");
        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }

    public function storeMedia(Request $request)
    {
        $media_folder_path = config("wagenabled.path.doc.watch_and_learn_media_path");
        $photos_thumb_path = $media_folder_path."thumb";

        $uploadedImgs = [];

        $photos = $request->file('file');

        if (!is_array($photos)) {
            $photos = [$photos];
        }

        if (!Storage::exists($media_folder_path)) {
            Storage::makeDirectory($media_folder_path);
        }

        if (!Storage::exists($photos_thumb_path)) {
            Storage::makeDirectory($photos_thumb_path);
        }

        $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < count($photos); $i++) {
            $photo = $photos[$i];
           
            $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 5);

            $timestamp = Carbon::createFromFormat('U.u', microtime(true))->format("YmdHisu");
            $nameWithoutExtension = preg_replace("/[^a-zA-Z0-9]/", "", '') . '' . $timestamp;
            $nameWithoutExtension = $nameWithoutExtension.'-'.$randomString;

            $fileExtension = $photo->getClientOriginalExtension();
            if ($fileExtension == '') {
                $fileExtension = $photo->guessClientExtension();
            }

            $name = $nameWithoutExtension. '.' . $fileExtension;
            $save_name = str_replace([' ', ':', '-'], "", $name);            

            $original_photo = Image::make($photo)
                            ->orientate()
                            ->encode($fileExtension); 

            $resize_photo = Image::make($photo)
                            ->orientate()
                            ->encode($fileExtension); 

            Storage::put($media_folder_path . $save_name, $original_photo);
            Storage::put($photos_thumb_path . '/' . $save_name, $resize_photo);

            $upload = new WatchAndLearnMedias();
            $upload->filename = $save_name;        
            $upload->original_name = basename($photo->getClientOriginalName());
            $upload->save();


            //$upload->img_url = Storage::url($media_folder_path . $save_name);
            $upload->img_thumb_url = Storage::url($photos_thumb_path."/" . $save_name);           
            $upload->img_url = Storage::url($photos_thumb_path."/" . $save_name);           
            array_push($uploadedImgs, $upload);
        }

        if ($request->ajax()) {
            return response()->json([
                'data' => $uploadedImgs
            ]);
        }

        return redirect()->back()->with('success', 'Media added successfully');
    }

    public function changeStatus($id, $status, Request $request)
    {
        $result = $this->model->find($id);                
        if ($result) {
            $result->status = $status;
            $result->Save();           
            $result['message'] =  "success";
            $result['code'] = 200;
        } else {
            
            $result['message'] = "Sorry, Watch and learn not found";
            $result['code'] = 400;
        }

        return response()->json($result, $result['code']);
    }  

    public function destroy($id)
    {
        $result = array();
        $data = $this->model->find($id);
        if ($data) {                    
            $res = $data->delete();
            if ($res) {
                $result['message'] =  "Watch and learn deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting watch and learn";
                $result['code'] = 400;
            }                        
        } else {
            $result['message'] = "Watch and learn not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
	}
	
	public function CommentList(Request $request, $slug, $lastId = 0, $parent_id = 0) {

        $responseData = []; 
        $comments = [];
       
        $perPage = config("wagenabled.no_of_comment_display", 6);    
        $NoOfchildrenCount = config("wagenabled.no_of_comment_children_display", 2);      
        $depth = config("wagenabled.comment_depth", 2);   

        $responseData["comments"] = [];
        $responseData["children_count"] = 0;
        $responseData["no_of_comments_display"] = $perPage;
        $responseData["no_of_children_display"] = $NoOfchildrenCount;
        $responseData["depth_count"] = $depth;
        $responseData["comment_count"] = 0;
        $message = ""; 
        $statusCodes = 200;

        $watch_and_learn = $this->model->Where('slug', $slug)
                            ->Where('status', 'published')
                            ->first();

        if( $watch_and_learn ) {   

            $total_count = WatchAndLearnComment::where('parent_comment_id', 0)
                                ->where("watch_and_learn_id", $watch_and_learn->id)
                                ->count();

            $total_count = $total_count ? $total_count : 0;                            
            if( $lastId == 0 ) {
                $last_comment = WatchAndLearnComment::select('id')
                                ->where("watch_and_learn_id", $watch_and_learn->id)                                    
                                ->orderBy('id', 'desc')
                                ->first(); 
                if( $last_comment ){                        
                    $lastId = $last_comment->id;
                    $lastId++;
                }
            } 

            if( $lastId !=0 ) {     
                //Comment ids get           
                $ids = WatchAndLearnComment::where('parent_comment_id', $parent_id)
                                    ->where("watch_and_learn_id", $watch_and_learn->id)
                                    ->where("id", '<', $lastId)
                                    ->orderBy('id', 'desc')
                                    ->limit($perPage)
                                    ->pluck('id')
                                    ->toArray(); 

                //Children Comments -          
                $childrenIds = [];
                $parentId = $ids;
                for ($i=1; $i <=$depth ; $i++) {                         
                    $newParentIds = [];                       
                    foreach ($parentId as $id) {
                        $childrenId = WatchAndLearnComment::where('parent_comment_id', $id)
                                        ->orderBy('id', 'desc')
                                        ->limit($NoOfchildrenCount)
                                        ->pluck('id')
                                        ->toArray();   
                        $childrenIds = array_merge($childrenIds, $childrenId);
                        $newParentIds = array_merge($newParentIds, $childrenId);
                    }
                    $parentId = $newParentIds;
                }
            
                $ids = array_merge($ids, $childrenIds);                   
                $comments = WatchAndLearnComment::with('user')->whereIn('id', $ids)
                                    ->orderBy('id', 'desc')                                    
                                    ->get()
                                    ->toArray();                       
                $count = [];

                foreach ($ids as $id ) {
                    $count[$id] = WatchAndLearnComment::where('parent_comment_id', $id)
                                        ->where("watch_and_learn_id", $watch_and_learn->id)
                                        ->count();
                }

                $responseData["comments"] = WagEnabledHelpers::buildTreeStructure($comments, $parent_id);
                $responseData["children_count"] = $count;
                $responseData["no_of_comments_display"] = $perPage;
                $responseData["no_of_children_display"] = $NoOfchildrenCount;
                $responseData["depth_count"] = $depth;
            }

            $responseData["comment_count"] = $total_count;                               
            $statusCodes = 200;    
            $message = "";   

        }

        return WagEnabledHelpers::apiJsonResponse($responseData, $statusCodes, $message);       
	}
	
	public function deleteComment(Request $request, $slug, $id) 
	{
		$responseData = [];
		$code = 403;
		$message = "Please, try again!";

        $watch_and_learn = $this->model->Where('slug', $slug)
                                ->first();
        if( $watch_and_learn ) {
            $watch_and_learn_comment = WatchAndLearnComment::with('allChildren')
                                        ->where('watch_and_learn_id', $watch_and_learn->id)
                                        ->where('id', $id)
                                        ->first(); 

            if( $watch_and_learn_comment ) {     

                $this->watch_and_learn_comment_ids[] = $watch_and_learn_comment->id;
                $this->getChildreanIDs($watch_and_learn_comment->allChildren);
               
                $comments = WatchAndLearnComment::with('user')->whereIn('id', $this->watch_and_learn_comment_ids)->delete();                  
                $message = "Comment deleted successfully";
                $code = $this->statusCodes['success']; 
            } 
        }
        return WagEnabledHelpers::apiJsonResponse($responseData, $code, $message);
	}
	
    public function getChildreanIDs($watch_and_learn_comments)
    {   
        $childrenId = [];
        foreach ($watch_and_learn_comments as $watch_and_learn_comment) {
            $childrenId[] = $watch_and_learn_comment->id;
            $this->watch_and_learn_comment_ids = array_merge($this->watch_and_learn_comment_ids, $childrenId);
            $this->getChildreanIDs($watch_and_learn_comment->allChildren);
        }         
    }
}
