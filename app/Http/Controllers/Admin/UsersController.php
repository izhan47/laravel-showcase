<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class UsersController extends Controller
{
    public function __construct(User $model)
    {        
        $this->moduleName = "Users";
        $this->singularModuleName = "User";
        $this->moduleRoute = url('admin/users');
        $this->moduleView = "admin.main.users";
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
        $result = $this->model->select("*")->with('city')->orderBy('id', 'desc');
        return Datatables::of($result)
        ->addColumn('city_state', function ($result) {
            $str = "";
            if( $result->city_id  ) {
                $str .= $result->city->name;
            }
            if( $result->state_id ) {
                $str .= ', '. $result->state->name;
            }
            return $str;            
        })
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
        $result = $this->model->with('pets', 'pets.breed')->find($id);
        if ($result) {
            return view("$this->moduleView.show", compact("result"));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Admin user not found");
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
