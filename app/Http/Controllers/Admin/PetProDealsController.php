<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PetProDealRequest;
use App\Models\PetPro;
use App\Models\PetProDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class PetProDealsController extends Controller
{
    public function __construct(PetProDeal $model)
    {            
        $this->moduleName = "Pet Pro Deals";
        $this->singularModuleName = "Pet Pro Deal";
        $this->moduleView = "admin.main.pet-pro-deals";
        $this->moduleRoute = url('admin/pet-pros');
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('moduleView', $this->moduleView);
    }

    public function index()
    {
        view()->share('isIndexPage', true);
        return view("$this->moduleView.index");
    }

    public function getDatatable(Request $request, $pet_pro_id)
    {
        $moduleRoute = url('admin/pet-pros/'.$pet_pro_id.'/deals');
        View::share('module_route', $moduleRoute);

        $result = $this->model->select("*")->where('pet_pro_id', $pet_pro_id)->orderBy('id', 'desc');
        return Datatables::of($result)
        ->addColumn('claimed', function ($result) {            
            return $result->claims->count();            
        })
        ->addIndexColumn()
        ->make(true);        
    }
    
    public function create($pet_pro_id)
    {
        $petPro = PetPro::find($pet_pro_id);
        if( $petPro ) {
            $moduleRoute = url('admin/pet-pros/'.$pet_pro_id.'/deals');
            View::share('module_route', $moduleRoute);
            return view("admin.main.pet-pro-deals.create", compact('pet_pro_id'));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");
    }
 
    public function store(PetProDealRequest $request, $pet_pro_id)
    {
        $input = $request->except(['_token']);
        try {    
            $petPro = PetPro::find($pet_pro_id);
            if( $petPro ) {
                $input["pet_pro_id"] = $pet_pro_id;
                $isSaved = $this->model->create($input);
                if ($isSaved) {
                    return redirect($this->moduleRoute.'/'.$pet_pro_id.'/edit')->with("success", "Pet pro deal created");
                }            
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }

    }

    public function edit($pet_pro_id, $id)
    {
        $petPro = PetPro::find($pet_pro_id);
        if( $petPro ) {
            $moduleRoute = url('admin/pet-pros/'.$pet_pro_id.'/deals');
            View::share('module_route', $moduleRoute);
            $result = $this->model->find($id);
            if ($result) {
                return view("admin.main.pet-pro-deals.edit", compact("result", "pet_pro_id"));
            }
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro deal not found");
    }
   
    public function update(PetProDealRequest $request, $pet_pro_id, $id)
    {               
        $input = $request->except(['_token']);       
        try {
            $petPro = PetPro::find($pet_pro_id);
            if( $petPro ) {
                $result = $this->model->find($id);            
                if ($result) {                                  
                    $isSaved = $result->update($input);        
                    if ($isSaved) {
                        return redirect($this->moduleRoute.'/'.$pet_pro_id.'/edit')->with("success", "Pet pro deal updated");
                    }
                }
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {            
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }
    
    public function changeStatus($pet_pro_id, $id)
    {
        $result = array();
        $petPro = PetPro::find($pet_pro_id);     

        if ($petPro) {            
            $data = $this->model->find($id);
            if ($data) { 
                
                if($data->status == 'active' ) {
                    $data->status = 'pause';
                } else {
                    $data->status = 'active';
                }
                $isSaved = $data->save();

                if ($isSaved) {
                    $result['message'] =  "Change pet pro status";
                    $result['code'] = 200;
                } else {
                    $result['message'] = "Error while change pet pro status";
                    $result['code'] = 400;
                }
            } else {
                $result['message'] = "Pet pro deal not Found!";
                $result['code'] = 400;
            }                                
        } else {
            $result['message'] = "Sorry, Something went wrong please try again!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }

    public function destroy($pet_pro_id, $id)
    {
        $result = array();
        $petPro = PetPro::find($pet_pro_id);     

        if ($petPro) {            
            $data = $this->model->find($id);
            if ($data) { 
                $res = $data->delete();
                if ($res) {
                    $result['message'] =  "Pet pro deal deleted.";
                    $result['code'] = 200;
                } else {
                    $result['message'] = "Error while deleting pet pro deal";
                    $result['code'] = 400;
                }
            } else {
                $result['message'] = "Pet pro deal not Found!";
                $result['code'] = 400;
            }                                
        } else {
            $result['message'] = "Sorry, Something went wrong please try again!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
