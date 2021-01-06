<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WatchAndLearnCategoriesRequest;
use App\Models\WatchAndLearnCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class ProductReviewCategoriesController extends Controller
{
    public function __construct(WatchAndLearnCategory $model)
    {        
        $this->moduleName = "Product Review Categories";
        $this->singularModuleName = "Product Review Category";
        $this->moduleRoute = url('admin/product-review-categories');
        $this->moduleView = "admin.main.product-review-categories";
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
        $result = $this->model->select("*")->ProductReviewCategory()->orderBy('name', 'asc');

        return Datatables::of($result)->addIndexColumn()->make(true);        
    }
    
    public function create()
    {
        return view("admin.main.general.create");
    }
 
    public function store(WatchAndLearnCategoriesRequest $request)
    {
        $input = $request->except(['_token']);
	
		$isExist = $this->model->where('name', '=', $input['name'])->where('parent_id', config("wagenabled.product_review_category_id"))->withTrashed()->first();
		if($isExist){
			if($isExist->deleted_at == null){
				return redirect($this->moduleRoute)->with("error", "Sorry, Product Review category alerady exist");
			} else {
				$isExist->deleted_at = null;
				$isExist->save();
				return redirect($this->moduleRoute)->with("success", "Product Review category created");
			}
		}
		else {
			try {      
                $input['parent_id'] = config("wagenabled.product_review_category_id");        
				$isSaved = $this->model->create($input);
				if ($isSaved) {
					return redirect($this->moduleRoute)->with("success", "Product Review category created");
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
        return redirect($this->moduleRoute)->with("error", "Sorry, Product Review category not found");
    }
   
    public function update(WatchAndLearnCategoriesRequest $request, $id)
    {               
		$input = $request->except(['_token']); 
		
		$isExist = $this->model->where('name', '=', $input['name'])->where('parent_id', config("wagenabled.product_review_category_id"))->where('id', '!=', $id)->first();
		if($isExist){
			return redirect($this->moduleRoute)->with("error", "Sorry, Product Review category alerady exist");
		} else {
			try {
				$result = $this->model->find($id);            
				if ($result) {                                  
					$isSaved = $result->update($input);        
					if ($isSaved) {
						return redirect($this->moduleRoute)->with("success", "Product Review category updated");
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
                $result['message'] =  "Product Review category deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting Product Review category";
                $result['code'] = 400;
            }
                       
           
        } else {
            $result['message'] = "Product Review category not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
