<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WatchAndLearnCategoriesRequest;
use App\Models\WatchAndLearnCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class WatchAndLearnCategoriesController extends Controller
{
    public function __construct(WatchAndLearnCategory $model)
    {        
        $this->moduleName = "Watch And Learn Categories";
        $this->singularModuleName = "Watch And Learn Category";
        $this->moduleRoute = url('admin/watch-and-learn-categories');
        $this->moduleView = "admin.main.watch-and-learn-categories";
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
        $result = $this->model->select("*")->GetWatchAndLearnCategory()->orderBy('name', 'asc');

        return Datatables::of($result)->addIndexColumn()->make(true);        
    }
    
    public function create()
    {
        return view("admin.main.general.create");
    }
 
    public function store(WatchAndLearnCategoriesRequest $request)
    {
        $input = $request->except(['_token']);
	
		$isExist = $this->model->where('name', '=', $input['name'])->where('parent_id', '!=', config("wagenabled.product_review_category_id"))->where('watch_and_learn_categories.parent_id', 0)->withTrashed()->first();
		if($isExist){
			if($isExist->deleted_at == null){
				return redirect($this->moduleRoute)->with("error", "Sorry, Watch and learn category alerady exist");
			} else {
				$isExist->deleted_at = null;
				$isExist->save();
				return redirect($this->moduleRoute)->with("success", "Watch and learn category created");
			}
		}
		else {
			try {           
				$isSaved = $this->model->create($input);
				if ($isSaved) {
					return redirect($this->moduleRoute)->with("success", "Watch and learn category created");
				}
				return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

			} catch (\Exception $e) {
				return redirect($this->moduleRoute)->with('error', $e->getMessage());
			}
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
        return redirect($this->moduleRoute)->with("error", "Sorry, Watch and learn category not found");
    }
   
    public function update(WatchAndLearnCategoriesRequest $request, $id)
    {               
		$input = $request->except(['_token']); 
		
		$isExist = $this->model->where('name', '=', $input['name'])->where('parent_id', '!=', config("wagenabled.product_review_category_id"))->where('watch_and_learn_categories.parent_id', 0)->where('id', '!=', $id)->first();
		if($isExist){
			return redirect($this->moduleRoute)->with("error", "Sorry, Watch and learn category alerady exist");
		} else {
			try {
				$result = $this->model->find($id);            
				if ($result) {                                  
					$isSaved = $result->update($input);        
					if ($isSaved) {
						return redirect($this->moduleRoute)->with("success", "Watch and learn category updated");
					}
				}
				return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

			} catch (\Exception $e) {            
				return redirect($this->moduleRoute)->with('error', $e->getMessage());
			}
		}
    }
  
    public function destroy($id)
    {
        $result = array();

        $data = $this->model->find($id);

        if ($data) {            
          
            $res = $data->delete();
            if ($res) {
                $result['message'] =  "Watch and learn category deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting watch and learn category";
                $result['code'] = 400;
            }
                       
           
        } else {
            $result['message'] = "Watch and learn category not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
