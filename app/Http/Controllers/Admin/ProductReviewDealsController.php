<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WatchAndLearnDealRequest;
use App\Models\WatchAndLearn;
use App\Models\WatchAndLearnDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class ProductReviewDealsController extends Controller
{
    public function __construct(WatchAndLearnDeal $model)
    {            
        $this->moduleName = "Product Review Deals";
        $this->singularModuleName = "Product Review Deal";
        $this->moduleView = "admin.main.product-review-deals";
        $this->moduleRoute = url('admin/product-reviews');
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

    public function getDatatable(Request $request, $watch_and_learn_id)
    {
        $moduleRoute = url('admin/product-reviews/'.$watch_and_learn_id.'/deals');
        View::share('module_route', $moduleRoute);

        $result = $this->model->select("*")->where('watch_and_learn_id', $watch_and_learn_id)->orderBy('id', 'desc');
        return Datatables::of($result)
        ->addColumn('claimed', function ($result) {            
            return $result->claims->count();            
        })
        ->addIndexColumn()
        ->make(true);        
    }
    
    public function create($watch_and_learn_id)
    {
        $watchAndLearn = WatchAndLearn::find($watch_and_learn_id);
        if( $watchAndLearn ) {
            $moduleRoute = url('admin/product-reviews/'.$watch_and_learn_id.'/deals');
            View::share('module_route', $moduleRoute);         
            return view("admin.main.product-review-deals.create", compact('watch_and_learn_id'));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");
    }
 
    public function store(WatchAndLearnDealRequest $request, $watch_and_learn_id)
    {
        $input = $request->except(['_token']);
        try {    
            $watchAndLearn = WatchAndLearn::find($watch_and_learn_id);
            if( $watchAndLearn ) {
                $input["watch_and_learn_id"] = $watch_and_learn_id;
                $isSaved = $this->model->create($input);
                if ($isSaved) {
                    return redirect($this->moduleRoute.'/'.$watch_and_learn_id.'/edit')->with("success", "Pet pro deal created");
                }            
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }

    }

    public function edit($watch_and_learn_id, $id)
    {
        $watchAndLearn = WatchAndLearn::find($watch_and_learn_id);
        if( $watchAndLearn ) {
            $moduleRoute = url('admin/product-reviews/'.$watch_and_learn_id.'/deals');
            View::share('module_route', $moduleRoute);
            $result = $this->model->find($id);
            if ($result) {
                return view("admin.main.product-review-deals.edit", compact("result", "watch_and_learn_id"));
            }
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro deal not found");
    }
   
    public function update(WatchAndLearnDealRequest $request, $watch_and_learn_id, $id)
    {               
        $input = $request->except(['_token']);       
        try {
            $watchAndLearn = WatchAndLearn::find($watch_and_learn_id);
            if( $watchAndLearn ) {
                $result = $this->model->find($id);            
                if ($result) {                                  
                    $isSaved = $result->update($input);        
                    if ($isSaved) {
                        return redirect($this->moduleRoute.'/'.$watch_and_learn_id.'/edit')->with("success", "Pet pro deal updated");
                    }
                }
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {            
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }
    
    public function changeStatus($watch_and_learn_id, $id)
    {
        $result = array();
        $watchAndLearn = WatchAndLearn::find($watch_and_learn_id);     

        if ($watchAndLearn) {            
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

    public function destroy($watch_and_learn_id, $id)
    {
        $result = array();
        $watchAndLearn = WatchAndLearn::find($watch_and_learn_id);     

        if ($watchAndLearn) {            
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
