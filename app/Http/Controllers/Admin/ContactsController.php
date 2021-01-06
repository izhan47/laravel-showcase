<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class ContactsController extends Controller
{
    public function __construct(Contact $model)
    {        
        $this->moduleName = "Contacts";
        $this->singularModuleName = "Contact";
        $this->moduleRoute = url('admin/contacts');
        $this->moduleView = "admin.main.contacts";
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
        ->editColumn('message', function ($result) {
            if( $result->message  ) {
                if( strlen($result->message) > 50 ){
                    return substr($result->message, 0, 50).'...';
                }
            }
            return $result->message;            
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
        $result = $this->model->find($id);
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
            
    }
}
