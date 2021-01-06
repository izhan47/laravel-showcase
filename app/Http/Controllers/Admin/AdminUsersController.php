<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUsersRequest;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class AdminUsersController extends Controller
{
    public function __construct(AdminUser $model)
    {        
        $this->moduleName = "Admin Users";
        $this->singularModuleName = "Admin User";
        $this->moduleRoute = url('admin/admin-users');
        $this->moduleView = "admin.main.admin-users";
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

        return Datatables::of($result)->addIndexColumn()->make(true);        
    }
    
    public function create()
    {
        return view("admin.main.general.create");
    }
 
    public function store(AdminUsersRequest $request)
    {
        $input = $request->except(['_token']);

        try {
            $input['password'] = Hash::make($request->password);
            $isSaved = $this->model->create($input);

            if ($isSaved) {
                return redirect($this->moduleRoute)->with("success", "Admin user created");
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
        return redirect($this->moduleRoute)->with("error", "Sorry, Admin user not found");
    }
   
    public function update(AdminUsersRequest $request, $id)
    {               
        $ignore_field = ['_token'];
        if( ! $request->password ) {
           array_push($ignore_field ,'password');
        } 
        $input = $request->except($ignore_field);        
        try {
            $result = $this->model->find($id);            

            if ($result) {              
               
                if ($request->password != "" && $request->password) {
                    $input['password'] = Hash::make($request->password);
                }

                $isSaved = $result->update($input);
            
                if ($isSaved) {
                    return redirect($this->moduleRoute)->with("success", "Admin user updated");
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
            if( ! $data->is_super ) {
                $res = $data->delete();
                if ($res) {
                    $result['message'] =  "Admin user deleted.";
                    $result['code'] = 200;
                } else {
                    $result['message'] = "Error while deleting admin user";
                    $result['code'] = 400;
                }
            }
            else {
                $result['message'] = "You can not delete super admin!";
                $result['code'] = 400;
            }
           
        } else {
            $result['message'] = "Admin user not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
