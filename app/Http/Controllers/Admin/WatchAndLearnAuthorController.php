<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WatchAndLearnAuthorRequest;
use App\Http\WagEnabledHelpers;
use App\Models\WatchAndLearn;
use App\Models\WatchAndLearnAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class WatchAndLearnAuthorController extends Controller
{
    public function __construct(WatchAndLearnAuthor $model)
    {        
        $this->moduleName = "Watch And Learn Authors";
        $this->singularModuleName = "Watch And Learn Author";
        $this->moduleRoute = url('admin/watch-and-learn-author');
        $this->moduleView = "admin.main.watch-and-learn-author";
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('module_route', $this->moduleRoute);
        View::share('moduleView', $this->moduleView);
    }

    public function index()
    {
        view()->share('isIndexPage', true);
        return view("$this->moduleView.index");
    }

    public function getDatatable(Request $request)
    {
        $result = $this->model->select("*")->orderBy('name', 'asc');
        return Datatables::of($result)
        ->editColumn('about', function ($result) {
            if( $result->about  ) {
                if( strlen($result->about) > 50 ){
                    return substr($result->about, 0, 50).'...';
                }
            }
            return $result->about;            
        })
        ->addIndexColumn()
        ->make(true);        
    }
    
    public function create()
    {        
        return view("admin.main.general.create");
    }
 
    public function store(WatchAndLearnAuthorRequest $request)
    {
        $input = $request->except(['_token', 'image','cropped_image']);

        try {   
            if ($request->file('image', false)) {        
                $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.watch_and_learn_author_path"), "", $isCreateThumb="1", $height=250, $width=380, $request->get('cropped_image'));            
                if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                    $input["profile_image"] = $imageStore['name'];                    
                }                    
            }     
            $isSaved = $this->model->create($input);
            if ($isSaved) {
                return redirect($this->moduleRoute)->with("success", "Watch and learn author created");
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }

    }

    public function show($id)
    {        

    }
    
    public function edit($id)
    {
        $result = $this->model->find($id);
        if ($result) {
            return view("admin.main.general.edit", compact("result"));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Watch and learn author not found");
    }
   
    public function update(WatchAndLearnAuthorRequest $request, $id)
    {               
        $input = $request->except(['_token', 'image','cropped_image']);      
        try {
            $result = $this->model->find($id);            
            if ($result) {   
                if ($request->file('image', false)) {        
                    $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.watch_and_learn_author_path"), $result->profile_image, $isCreateThumb="1", $height=250, $width=380, $request->get('cropped_image'));            
                    if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                        $input["profile_image"] = $imageStore['name'];                    
                    }                    
                }                
               
                $isSaved = $result->update($input);        
                if ($isSaved) {
                    return redirect($this->moduleRoute)->with("success", "Watch and learn author updated");
                }
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {            
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }
  
    public function destroy($id)
    {
        $result = array();
        $data = $this->model->find($id);
        if ($data) {                    
            $res = $data->delete();
            if ($res) {
                $result['message'] =  "Watch and learn author deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting watch and learn author";
                $result['code'] = 400;
            }                        
        } else {
            $result['message'] = "Watch and learn author not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
