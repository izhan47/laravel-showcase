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

class ProductReviewController extends Controller
{
    public function __construct(WatchAndLearn $model)
    {        
        $this->moduleName = "Product Reviews";
        $this->singularModuleName = "Product Review";
        $this->moduleRoute = url('admin/product-reviews');
        $this->moduleView = "admin.main.product-reviews";
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
		
		$categories = WatchAndLearnCategory::ProductReviewCategory()->pluck('name', 'id')->toArray();

        return view("$this->moduleView.index", compact('frontUrl', 'categories'));
    }

    public function getDatatable(Request $request)
    {
        $blogMode = $request->get('blogMode');
		$category_id = $request->get('category_id');

        $result = $this->model->with('category', 'author')->ProductReviewCategory()->select("*")->where('status', $blogMode)->orderBy('id', 'desc');

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
        $categories = WatchAndLearnCategory::ProductReviewCategory()->orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        $authors = WatchAndLearnAuthor::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        return view("$this->moduleView.create", compact('categories', 'authors'));
    }
 
    public function store(WatchAndLearnRequest $request)
    {
        $input = $request->except(['_token', 'image','cropped_image', 'blogMode']);
        try {   
            if ($request->file('image', false)) {        
                $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.watch_and_learn_thumbnail_path"), "", $isCreateThumb="1", $height=250, $width=380, $request->get('cropped_image'));            
                if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                    $input["thumbnail"] = $imageStore['name'];                    
                }                    
            } 

            $isSaved = $this->model->create($input);
            if ($isSaved) {
                if( $request->get('blogMode') == 'draft' ) {
                    return redirect($this->moduleRoute)->with("success", "Product Review created");
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
        return redirect($this->moduleRoute)->with("error", "Sorry, Product Review not found");            
    }
    
    public function edit($id)
    {
        $result = $this->model->find($id);
        $categories = WatchAndLearnCategory::ProductReviewCategory()->orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        $authors = WatchAndLearnAuthor::orderBy('name', 'asc')->get()->pluck("name", "id")->toArray();
        if ($result) {
            return view("$this->moduleView.edit", compact("result","categories", 'authors'));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Product Review not found");
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

                if( $request->get('blogMode') == 'draft' ) {                        
                    $input["status"] = 'draft';  
                }
                $isSaved = $result->update($input);  

                if ($isSaved) {                                    

                    if( $request->get('blogMode') == 'draft' ) {                        
                        return redirect($this->moduleRoute);
                    } 
                    if( $request->get('blogMode') == 'publish'  ) {
                        return redirect($this->moduleRoute);                        
                    }            

                    $viewData['watchAndLearn'] = $result;                      
                    return view("$this->moduleView.content-builder", $viewData);

                    return redirect($this->moduleRoute)->with("success", "Product Review updated");
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
        $WatchAndLearn = $this->model::whereId($id)->first();
        if ($WatchAndLearn) {
            $viewData['watchAndLearn'] = $WatchAndLearn;
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

    public function changeStatus($id, $status, Request $request)
    {
        $result = $this->model->find($id);                
        if ($result) {
            $result->status = $status;
            $result->Save();           
            $result['message'] =  "success";
            $result['code'] = 200;
        } else {
            
            $result['message'] = "Sorry, Product Review not found";
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
                $result['message'] =  "Product Review deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting Product Review";
                $result['code'] = 400;
            }                        
        } else {
            $result['message'] = "Product Review not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
	
	public function CommentList(Request $request, $slug, $lastId = 0, $parent_id = 0) {

        $this->responseData = []; 
        $comments = [];
       
        $perPage = config("wagenabled.no_of_comment_display", 6);    
        $NoOfchildrenCount = config("wagenabled.no_of_comment_children_display", 2);      
        $depth = config("wagenabled.comment_depth", 2);   

        $this->responseData["comments"] = [];
        $this->responseData["children_count"] = 0;
        $this->responseData["no_of_comments_display"] = $perPage;
        $this->responseData["no_of_children_display"] = $NoOfchildrenCount;
        $this->responseData["depth_count"] = $depth;
        $this->responseData["comment_count"] = 0;
                
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

                $this->responseData["comments"] = WagEnabledHelpers::buildTreeStructure($comments, $parent_id);
                $this->responseData["children_count"] = $count;
                $this->responseData["no_of_comments_display"] = $perPage;
                $this->responseData["no_of_children_display"] = $NoOfchildrenCount;
                $this->responseData["depth_count"] = $depth;
            }

            $this->responseData["comment_count"] = $total_count;                               
            $this->code = $this->statusCodes['success'];    
            $this->message = "";   

        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);       
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
