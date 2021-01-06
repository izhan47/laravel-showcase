<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Http\WagEnabledHelpers;

class TestimonialController extends Controller
{
    public function __construct(Testimonial $model) {
        $this->moduleName = "Testimonial";
        $this->singularModuleName = "Testimonial";
        $this->moduleRoute = url('admin/testimonial');
        $this->moduleView = "admin.main.testimonials";
        $this->model = $model;

        $this->breadcrumb = [['title' => $this->moduleName, 'url' => $this->moduleRoute]];

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('module_route', $this->moduleRoute );
		View::share('moduleView', $this->moduleView );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        view()->share('isIndexPage', true);
        return view("$this->moduleView.index");
    }

    public function getDatatable()
    {  
        $result = $this->model->all();
        return Datatables::of($result)->addIndexColumn()->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->breadcrumb[] = ['title' => "Add ".$this->moduleName, 'url' => ''];
        view()->share('breadcrumb', $this->breadcrumb);

        return view("admin.main.general.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TestimonialRequest $request)
    {
        $input = $request->except(['_token', 'image','cropped_image']);

        try {
			if ($request->file('image', false)) {        
                $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.testimonial_image_path"), "", $isCreateThumb="1", $height=350, $width=300, $request->get('cropped_image'));            
                if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                    $input["image"] = $imageStore['name'];                    
                }                    
            } 
            $isSaved = $this->model->create($input);

            if ($isSaved) {
                return redirect($this->moduleRoute)->with("success", $this->moduleName." Added Successfully");
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = $this->model->find($id);
        if($result) {
            $this->breadcrumb[] = ['title' => "Edit ".$this->moduleName, 'url' => ''];
            view()->share('breadcrumb', $this->breadcrumb);

            return view("admin.main.general.edit", compact("result"));
        }
        return redirect($this->moduleRoute)->with("error", "Sorry, $this->moduleName not found");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TestimonialRequest $request, $id)
    {
        $input = $request->except(['_token', 'image','cropped_image']);
        try {
            $result = $this->model->find($id);

            if($result) {
                if ($request->file('image', false)) {        
                    $imageStore = WagEnabledHelpers::saveUploadedImage($request->file('image'), config("wagenabled.path.doc.testimonial_image_path"), $result->image, $isCreateThumb="1", $height=350, $width=300, $request->get('cropped_image'));            
                    if (isset($imageStore['error_msg']) && $imageStore['error_msg'] == '' && isset($imageStore['name']) && !empty($imageStore['name'])) {
                        $input["image"] = $imageStore['name'];                    
                    }                    
                } 
                $isSaved = $result->update($input);
                
                if ($isSaved) {
                    return redirect($this->moduleRoute)->with("success", $this->moduleName." Updated Successfully");
                }
            }
            return redirect($this->moduleRoute)->with("error", "Sorry, Something went wrong please try again");

        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = array();
        try {
            $res = $this->model->find($id);
            if ($res) {
                $res->delete();

                $result['message'] = "$this->moduleName Deleted Successfully.";
                $result['code'] = 200;
            } else {
                $result['code'] = 400;
                $result['message'] = "Something went wrong";
            }
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            $result['code'] = 400;
        }

        return response()->json($result, $result['code']);
    }
}
