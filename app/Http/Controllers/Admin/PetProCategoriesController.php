<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PetProCategoriesRequest;
use App\Models\PetProCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class PetProCategoriesController extends Controller
{
    public function __construct(PetProCategory $model)
    {        
        $this->moduleName = "Pet Pro Categories";
        $this->singularModuleName = "Pet Pro Category";
        $this->moduleRoute = url('admin/pet-pro-categories');
        $this->moduleView = "admin.main.pet-pro-categories";
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
        $result = $this->model->withCount('petPro')->orderBy('name', 'asc');

        return Datatables::of($result)->addIndexColumn()->make(true);        
    }
    
    public function create()
    {
        return view("admin.main.general.create");
    }
 
    public function store(PetProCategoriesRequest $request)
    {
		$input = $request->except(['_token']);
		
		$isExist = $this->model->where('name', '=', $input['name'])->withTrashed()->first();
		if($isExist){
			if($isExist->deleted_at == null){
				return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro category alerady exist");
			} else {
				$isExist->deleted_at = null;
				$isExist->save();
				return redirect($this->moduleRoute)->with("success", "Pet pro category created");
			}
		}
        try {           
            $isSaved = $this->model->create($input);
            if ($isSaved) {
                return redirect($this->moduleRoute)->with("success", "Pet pro category created");
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
        return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro category not found");
    }
   
    public function update(PetProCategoriesRequest $request, $id)
    {               
		$input = $request->except(['_token']);      
		$isExist = $this->model->where('name', '=', $input['name'])->where('id', '!=', $id)->first();
		if($isExist){
			return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro category alerady exist");
		} else { 
			try {
				$result = $this->model->find($id);            
				if ($result) {                                  
					$isSaved = $result->update($input);        
					if ($isSaved) {
						return redirect($this->moduleRoute)->with("success", "Pet pro category updated");
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
                $result['message'] =  "Pet pro category deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting watch and learn category";
                $result['code'] = 400;
            }
                       
           
        } else {
            $result['message'] = "Pet pro category not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
