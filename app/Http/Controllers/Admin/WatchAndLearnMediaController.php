<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WatchAndLearnRequest;
use App\Http\WagEnabledHelpers;
use App\Models\WatchAndLearnMedias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Storage;
use Yajra\Datatables\Datatables;

class WatchAndLearnMediaController extends Controller
{
    public function __construct(WatchAndLearnMedias $model)
    {
        $this->moduleName = "Watch And Learn Medias";
        $this->singularModuleName = "Watch And Learn Media";
        $this->moduleRoute = url('admin/watch-and-learn-medias');
        $this->moduleView = "admin.main.watch-and-learn-medias";
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('singular_module_name', $this->singularModuleName);
        View::share('module_route', $this->moduleRoute);
        View::share('moduleView', $this->moduleView);
    }

    public function index(Request $request)
    {
        view()->share('isIndexPage', true);
        $data['html'] = $this->load_more($request);
        return view("$this->moduleView.index",  $data);
    }

     public function load_more(Request $request)
    {
        $media = $this->model::select('*');

        $count = $media->count();
        $photos = $media->latest('id')->paginate(4);
        $isMoreRecords = $photos->toArray()["next_page_url"] ? true : false;
        $photos->map(function ($photo) {
            $media_folder_path = config("wagenabled.path.doc.watch_and_learn_media_path");
            $photos_thumb_path = $media_folder_path."thumb";

            //$photo['img_url'] = config('app.url') . "/storage/$media_folder_path/" . $photo['filename'];
            //$photo['img_thumb_url'] = config('app.url') . "/storage/$photos_thumb_path/" . $photo['filename'];
            $photo['img_url'] = Storage::url($media_folder_path . $photo['filename']);
            $photo['img_thumb_url'] = Storage::url($photos_thumb_path."/" . $photo['filename']);

            return $photo;
        });

        $html = null;
        if (!$photos->isEmpty()) {
            foreach ($photos as $photo) {
                $html .= '<div class="col-md-4 col-xl-3 file-box"><div class="file"><div class="image">';

                $html .= '<img alt="Image not found" class="img-fluid" src="' . $photo->img_thumb_url . '" title="' . $photo->original_name . '" onerror="this.src=\'' . url('admin-theme/images/default.png') . '\'">';

                $html .= '</div><div class="file-name">';

                $html .= '<span class="">' . \Str::limit($photo->original_name, 10) . '</span>';

                $html .= '<div class=""><div class="btn-group">';

                $html .= '<button class="btn btn-success btn-xs" data-url="' . $photo->img_url . '" onclick="copyImgUrl(this)" title="Copy URL"><i class="fa fa-copy"></i></button>';

                $html .= '<button class="btn btn-info btn-xs" data-url="' . $photo->img_thumb_url . '" onclick="copyImgUrl(this)" title="Copy thumb URL"><i class="fa fa-copy"></i></button>';


                    $html .= '<button class="btn btn-danger btn-xs delete-media" data-id="' . $photo->id . '" title="Delete"><i class="fa fa-trash"></i></button>';


                $html .= '</div><div class="clearfix"></div></div>';
                $html .= '</div></div></div>';
            }
            // if ($count > 2) {
            //     $html .= '<a href="javascript:;" id="loadMore" class="col-12"><div class="file-box"><div class="file"><div class="icon"><i class="fa fa-plus-square"></i></div><div class="file-name text-center">Load more</div></div></div></a>';
            // }
            if ($isMoreRecords) {
                $html .= '<div id="loadMore" class="col-12"><a href="javascript:;" class="wag-admin-btns-main"><i class="fa fa-plus-square"></i>Load more</a></div>';
            }
            // if ($count > 2) {
            //     $html .= '<div class="col-12"><a href="javascript:;" id="loadMore" class="wag-admin-btns-main"><i class="fa fa-plus-square"></i>Load more</a></div>';
            // }
        }

        if ($request->ajax()) {
            $response['html'] = $html;
            $response['status'] = true;
            return response()->json($response);
        } else {
            return $html;
        }
    }

    public function create()
    {
        return view("admin.main.general.create");
    }

    public function store(Request $request)
    {
        $media_folder_path = config("wagenabled.path.doc.watch_and_learn_media_path");
        $photos_thumb_path = $media_folder_path."thumb";

        $uploadedImgs = [];

        $photos = $request->file('file');

        if (!is_array($photos)) {
            $photos = [$photos];
        }

        if (!Storage::exists($media_folder_path)) {
            Storage::makeDirectory($media_folder_path);
        }

        if (!Storage::exists($photos_thumb_path)) {
            Storage::makeDirectory($photos_thumb_path);
        }


        $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < count($photos); $i++) {
            $photo = $photos[$i];
            if( $photo ) {
                $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 5);

                $timestamp = Carbon::createFromFormat('U.u', microtime(true))->format("YmdHisu");
                $nameWithoutExtension = preg_replace("/[^a-zA-Z0-9]/", "", '') . '' . $timestamp;
                $nameWithoutExtension = $nameWithoutExtension.'-'.$randomString;

                $fileExtension = $photo->getClientOriginalExtension();
                if ($fileExtension == '') {
                    $fileExtension = $photo->guessClientExtension();
                }

                $name = $nameWithoutExtension. '.' . $fileExtension;
                $save_name = str_replace([' ', ':', '-'], "", $name);

                $original_photo = Image::make($photo)
                            ->orientate()
                            ->encode($fileExtension);

                $resize_photo = Image::make($photo)
                            ->orientate()
                            ->encode($fileExtension, 50);

                Storage::put($media_folder_path . $save_name, $original_photo);
                Storage::put($photos_thumb_path . '/' . $save_name,  $resize_photo);

                $upload = new WatchAndLearnMedias();
                $upload->filename = $save_name;
                $upload->original_name = basename($photo->getClientOriginalName());
                $upload->save();


                $upload->img_url = Storage::url("/$media_folder_path/" . $save_name);
                $upload->img_thumb_url = Storage::url("/$photos_thumb_path/" . $save_name);
                array_push($uploadedImgs, $upload);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'data' => $uploadedImgs
            ]);
        }

        return redirect()->back()->with('success', 'Media added successfully');
    }

    public function destroy($id)
    {
        $media = $this->model::where('id', $id)->first();
        if ($media) {
            $media_folder_path = config("wagenabled.path.doc.watch_and_learn_media_path");
            $photos_thumb_path = $media_folder_path."thumb";

            $deleteFiles = [
                $media_folder_path . $media->filename,
                $photos_thumb_path . '/' . $media->filename,
            ];

            Storage::delete($deleteFiles);
            $media->delete();

            $response['status'] = true;
            $response['message'] = 'Media deleted successfully';
        } else {
            $response['status'] = false;
            $response['message'] = 'Media not found';
        }
        return response()->json($response);
    }
}
