<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Mail\SendCommentNotificaitonMail;
use App\Models\UserSavedVideo;
use App\Models\WatchAndLearn;
use App\Models\WatchAndLearnCategory;
use App\Models\WatchAndLearnComment;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class WatchAndLearnController extends Controller
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

        $watch_and_learn = WatchAndLearn::with('category')->GetWatchAndLearnCategory()->withCount('users')->Where('status', 'published');  

        if( $category_id ) {
            $watch_and_learn = $watch_and_learn->where( 'category_id', $category_id );
        }

        if( $search ) {
            $watch_and_learn = $watch_and_learn->where( function($query) use( $search ){ 
                                            $query->Where('title', 'like', '%'.$search.'%');
                                            $query->orWhere('description', 'REGEXP', '>[^<]*'.$search);
                                            /*$query->orwhereHas('author', function($query) use( $search ) {
                                                $$query->Where('name', 'like', '%'.$search.'%');
                                            });*/
                            
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

    public function getRelatedVideoList(Request $request, $slug) 
    {        
        $watch_and_learn = WatchAndLearn::Where('slug', $slug)->Where('status', 'published')
                                ->first();

        if( $watch_and_learn ) {        
            $this->responseData["related_video_list"] = WatchAndLearn::with('category')
                                                                ->where('category_id', $watch_and_learn->category_id)
                                                                ->where('id', '!=', $watch_and_learn->id)
                                                                ->Where('status', 'published')
                                                                ->orderBy('id', 'desc')                   
                                                                ->take(config('wagenabled.no_of_related_video_display', 3))
                                                                ->get();
            $this->message = "";
            $this->code = $this->statusCodes['success']; 
        }

       
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function getDetails(Request $request, $slug) {
        $watch_and_learn = WatchAndLearn::withCount(['comments' => function($query){
                                                                    $query->where('parent_comment_id', 0);
                                                                }])
                                
                                ->with(['category','author',
                                        'deals' => function($query){
                                            $query->active();
                                        }                                
                                    ])
                                ->Where('slug', $slug)
                                ->Where('status', 'published')
                                ->first();
        if( $watch_and_learn ) {
            $this->responseData["is_saved"] = 0;
            $user = Auth::user();
            if ($user) {
                if ($user->count() != 0) {
                    $this->responseData["is_saved"] = UserSavedVideo::where("user_id", $user->id)->where("watch_and_learn_id", $watch_and_learn->id)->first() ? 1 : 0;
                }
            }
            $this->responseData["watch_and_learn"] = $watch_and_learn;
            $this->message = "";
            $this->code = $this->statusCodes['success']; 
        }
        
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);

    }

    public function saveUnsaveVideos(Request $request, $slug) {
        
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $watch_and_learn = WatchAndLearn::Where('slug', $slug)->Where('status', 'published')
                                ->first();
        if( $watch_and_learn ) {
            $isSavedVideo = UserSavedVideo::where('user_id', $user->id)
                                        ->where('watch_and_learn_id', $watch_and_learn->id)
                                        ->first();
            if( $isSavedVideo ) {
                $isSavedVideo->delete();
                $this->responseData["is_saved"] = 0;
                $this->message = "Blog post removed from saved posts.";
            } else {
                $isSaved = UserSavedVideo::create([
                    'user_id' => $user->id,
                    'watch_and_learn_id' => $watch_and_learn->id
                ]);               
                $this->responseData["is_saved"] = 1;
                $this->message = 'Blog post saved. You can find it in your profile\'s "Saved posts" section in the future!';                
            }
            $this->code = $this->statusCodes['success']; 
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function storeComment(Request $request) {
        
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'parent_comment_id' => 'required | integer',
            'slug' => 'required',
            /*'name' => 'required|max:255',
            'email' => 'required | email', */        
            'message' => 'required',           
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }
        $slug = $request->get('slug');
        $watch_and_learn = WatchAndLearn::Where('slug', $slug)
                                ->Where('status', 'published')
                                ->first();
        if( $watch_and_learn ) {
            $input = $request->only(['parent_comment_id' , 'message']);
            $input["user_id"] = $user->id;
            
            $input["name"] = $user->name;
            /*$input["email"] = $user->email;*/

            $input["watch_and_learn_id"] = $watch_and_learn->id;

            $isSaved = WatchAndLearnComment::create($input);

            if ($isSaved) {

                // send email to Wag Enabled 
                Mail::to(config('wagenabled.send_comment_notification_to_email'))->send(new SendCommentNotificaitonMail($watch_and_learn));

                $this->responseData["comments"] = [WatchAndLearnComment::with('user')
                                    ->where("id", $isSaved->id)
                                    ->first()];
                $this->message = "Comment added successfully";            
                $this->code = $this->statusCodes['success'];                 
            }
        }

        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
    }

    public function deleteComment(Request $request, $slug, $id) {
        
        $user = Auth::user();
        if ($user->count() == 0) {
            return WagEnabledHelpers::apiUserNotFoundResponse();
        }

        $watch_and_learn = WatchAndLearn::Where('slug', $slug)
                                ->first();
        if( $watch_and_learn ) {
            $watch_and_learn_comment = WatchAndLearnComment::with('allChildren')->where('user_id', $user->id)
                                        ->where('watch_and_learn_id', $watch_and_learn->id)
                                        ->where('parent_comment_id', 0)
                                        ->where('id', $id)
                                        ->first(); 

            if( $watch_and_learn_comment ) {     

                $this->watch_and_learn_comment_ids[] = $watch_and_learn_comment->id;
                $this->getChildreanIDs($watch_and_learn_comment->allChildren);
               
                $comments = WatchAndLearnComment::with('user')->whereIn('id', $this->watch_and_learn_comment_ids)->delete();                  
                $this->message = "Comment deleted successfully";
                $this->code = $this->statusCodes['success']; 
            } 
        }
        return WagEnabledHelpers::apiJsonResponse($this->responseData, $this->code, $this->message);
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

    public function all_products()
    {
        $products = [];
        $Children = [$this];
        while(count($Children) > 0){
            $nextCategories = [];
            foreach ($Children as $child) {
                $products = array_merge($products, $child->products->all());
                $nextCategories = array_merge($nextCategories, $child->children->all());
            }
            $Children = $nextCategories;
        }
        return new Collection($products); //Illuminate\Database\Eloquent\Collection
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
                
        $watch_and_learn = WatchAndLearn::Where('slug', $slug)
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

    public function getCategoryList(Request $request) {

        $category_data = WatchAndLearnCategory::GetWatchAndLearnCategory()
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

}
