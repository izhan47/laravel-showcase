<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class NewslettersController extends Controller
{
    public function __construct(Newsletter $model)
    {        
        $this->moduleName = "Newsletters";
        $this->singularModuleName = "Newsletter";
        $this->moduleRoute = url('admin/newsletters');
        $this->moduleView = "admin.main.newsletters";
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
        $result = $this->model->select("*")->orderBy('id', 'desc');
        return Datatables::of($result)        
        ->addIndexColumn()
        ->make(true);        
    }
    
    public function create()
    {
        
    }
 
    public function store(Request $request)
    {       

    }

    public function show($id)
    {               
    }
    
    public function edit($id)
    {
       
    }
   
    public function update(Request $request, $id)
    {   

    }
  
    public function destroy($id)
    {
        $result = array();
        $data = $this->model->find($id);

        if ($data) {                   
            $res = $data->delete();
            if ($res) {
                $result['message'] =  "User deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting user";
                $result['code'] = 400;
            }          
           
        } else {
            $result['message'] = "User not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
