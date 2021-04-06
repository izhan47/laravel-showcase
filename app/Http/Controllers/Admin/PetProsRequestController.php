<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Models\PetPro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class PetProsRequestController extends Controller
{
    public function __construct(PetPro $model)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $this->moduleName = "Pet Pros";
        $this->singularModuleName = "Pet Pro";
        $this->moduleRoute = url('admin/pet-pros-request');
        $this->moduleView = "admin.main.pet-pro-request";
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('module_route', $this->moduleRoute);
        View::share('moduleView', $this->moduleView);
    }

    ///////////////
    // By Umar
    //////////////
    public function approvePetPro($id)
    {
        $petpros = $this->model->find($id);
        $data = [];
        if ($petpros) {
            $data = json_decode($petpros->new_detail);

            $petpros->update((array)$data);

            $petpros->new_detail = null;
            $petpros->status = "approved";
            $petpros->update();
            // return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.success"), "Pet Pros is approved.");
            return redirect($this->moduleRoute)->with("success", "Pet pro approved");
        } else {
            // return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.server_side"), "Something Went Wrong.");
            return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro approval failed");

        }
    }
    public function rejectPetPro($id)
    {
        $data = $this->model->find($id);
        if ($data) {
            $data->status = "reject";
            $data->update();
            // return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.success"), "Pet Pros is rejected.");
            return redirect($this->moduleRoute)->with("success", "Pet pro rejected");
        } else {
            // return WagEnabledHelpers::apiJsonResponse([], config("wagenabled.status_codes.server_side"), "Something Went Wrong.");
				return redirect($this->moduleRoute)->with("error", "Sorry, Pet pro rejection failed");

        }
    }
    public function getAllPetProsRequestDatatable()
    {
        
        view()->share('isIndexPage', true);
        return view($this->moduleView.".index");
    }
    public function getPetProsRequestDatatable(Request $request)
    {
        $result = $this->model->where('status',null)->orWhere('status','pending')->orWhere('status','approved')->where('new_detail','!=',null)->orderBy('id', 'desc')->get();
        return Datatables::of($result)
            ->editColumn('message', function ($result) {
                if ($result->message) {
                    if (strlen($result->message) > 50) {
                        return substr($result->message, 0, 50) . '...';
                    }
                }
                return $result->message;
            })
            ->addIndexColumn()
            ->make(true);
    }
    public function destroy($id)
    {
        $result = array();
        $data = $this->model->find($id);
        if ($data) {
            $res = $data->delete();
            if ($res) {
                $result['message'] = "Pet pro deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting pet pro";
                $result['code'] = 400;
            }
        } else {
            $result['message'] = "Pet pro not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
    public function show($id)
    {        
        $result = $this->model->find($id);
        if ($result) {
            return view("$this->moduleView.show", compact("result"));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, Admin user not found");
    }
}