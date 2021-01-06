<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Http\WagEnabledHelpers;

class ScriptCreatePetProSmallThumbImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:create-pet-pro-small-thumb {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script for create thumb images';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $this->info("*************************************");
        $this->info("Process Started....");
        $this->info("*************************************");

        $modyleType = $this->option('type');

        if($modyleType == "pet-pro") {
            $classObj = app()->make("App\Models\PetProGallery");
            $storagePath = config('wagenabled.path.doc.pet_pro_gallery_image_path');
            $imageColumn = "gallery_image";
        }                 
        else {
            $this->error("Invalid Type, Please double check it");
            return false;
        }

        $this->compressModuleWiseImages($classObj, $storagePath, $imageColumn);
    
        $this->info("*************************************");
        $this->info("Process Completed....");
        $this->info("*************************************");

    }

    protected function compressModuleWiseImages($model, $storagePath, $imageColumn = "image")
    {
        $images = $model->select(["id", $imageColumn])
                        ->whereNotNull($imageColumn)
                        ->get();


        $this->info("===>>>> Total pet pro Records: " . $images->count());
        $this->info("*************************************************");
        
        $totalimages = $images->count();
        if($totalimages) {
            $thumbPath = $storagePath . "thumb/";
            $smallThumbPath = $storagePath . "small-thumb/";
            $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            $recCount = 0;

            foreach ($images as $image) {
                try {
                   
                    $recCount++;
                    $oldImageName = $image->$imageColumn;
                    $filePathInfo = pathinfo($oldImageName);
                    $fileExtension = (isset($filePathInfo['extension']) && !empty($filePathInfo['extension'])) ? $filePathInfo['extension'] : "jpg" ;                  
                    
                    $oldThumbFilePath = $thumbPath . $oldImageName;


                    if( Storage::exists($oldThumbFilePath) ) {
                        $originalThumbImageContents = Storage::get($oldThumbFilePath);

                        $thumbImageContent = Image::make($originalThumbImageContents)
                                                    ->resize(500, null, function ($constraint) {
                                                        $constraint->aspectRatio();
                                                        $constraint->upsize();
                                                    })
                                                    ->orientate()
                                                    ->encode($fileExtension);                                                            
                        Storage::put($smallThumbPath . $oldImageName, $thumbImageContent);
                    }                                            

                    $this->info("----------------------------------");
                    $this->info("[". $recCount ."/$totalimages] Module ID: " . $image->id);
                    $this->info("New File: " . $oldImageName . "  <<-------->>  Old File: " . $oldImageName);
                    

                } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                    $this->error("Error for module id: " . $image->id);
                    $this->error("File Not Found: " . $e->getMessage());
                } catch (Exception $e) {
                    $this->error("Error for module id: " . $image->id);
                    $this->error($e->getMessage());
                }
            }
        }
    }
}
