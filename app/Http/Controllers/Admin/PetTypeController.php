<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PetTypeRequest;
use App\Models\PetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;

class PetTypeController extends Controller
{
    public function __construct(PetType $model)
    {
        $this->moduleName = "Pet Type";
        $this->singularModuleName = "Pet Type";
        $this->moduleRoute = url('admin/pet-type');
        $this->moduleView = "admin.main.pet-type";
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
        \Log::error(json_encode($result));
        return Datatables::of($result)->addIndexColumn()->make(true);
    }

    public function create()
    {
        return view("admin.main.general.create");
    }

    public function store(PetTypeRequest $request)
    {
        $input = $request->except(['_token']);

        $isExist = $this->model->where('name', '=', $input['name'])->withTrashed()->first();
        if ($isExist) {
            if ($isExist->deleted_at == null) {
                return redirect($this->moduleRoute)->with("error", "Sorry, Pet typ alerady exist");
            } else {
                $isExist->deleted_at = null;
                $isExist->save();
                return redirect($this->moduleRoute)->with("success", "Pet typee created");
            }
        }
        try {
            $isSaved = $this->model->create($input);
            if ($isSaved) {
                return redirect($this->moduleRoute)->with("success", "Pet type created");
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
        return redirect($this->moduleRoute)->with("error", "Sorry, Pet type not found");
    }

    public function update(PetTypeRequest $request, $id)
    {

        $input = $request->except(['_token']);
        $isExist = $this->model->where('name', '=', $input['name'])->where('id', '!=', $id)->first();
        if ($isExist) {
            return redirect($this->moduleRoute)->with("error", "Sorry, Pet type already exist");
        } else {
            try {
                $result = $this->model->find($id);
                if ($result) {
                    $isSaved = $result->update($input);
                    if ($isSaved) {
                        return redirect($this->moduleRoute)->with("success", "Pet type updated");
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
                $result['message'] = "Pet type deleted.";
                $result['code'] = 200;
            } else {
                $result['message'] = "Error while deleting watch and learn pet type";
                $result['code'] = 400;
            }

        } else {
            $result['message'] = "Pet type not Found!";
            $result['code'] = 400;
        }
        return response()->json($result, $result['code']);
    }
}
