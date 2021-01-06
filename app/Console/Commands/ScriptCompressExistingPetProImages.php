<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Http\WagEnabledHelpers;

class ScriptCompressExistingPetProImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:compress-existing-pet-pro-images {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script for compress existing images';

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
        $images = $model->select(["id", $imageColumn, 'is_cropped_image'])
                        ->whereNotNull($imageColumn)
                        ->get();


        $this->info("===>>>> Total pet pro Records: " . $images->count());
        $this->info("*************************************************");
        
        $totalimages = $images->count();
        if($totalimages) {
            $thumbPath = $storagePath . "thumb/";
            $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            $recCount = 0;

            foreach ($images as $image) {
                try {
                   
                    $recCount++;
                    $oldImageName = $image->$imageColumn;
                    $filePathInfo = pathinfo($oldImageName);
                    $fileExtension = (isset($filePathInfo['extension']) && !empty($filePathInfo['extension'])) ? $filePathInfo['extension'] : "jpg" ;
                    $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 7)), 0, 7);
                    
                    $name = $randomString . '-' . $oldImageName;
                    
                    $oldFilePath = $storagePath . $oldImageName;
                    $oldThumbFilePath = $thumbPath . $oldImageName;


                    $originalImageContents = Storage::get($oldFilePath);
                    $originalImage = Image::make($originalImageContents)
                                                ->orientate()
                                                ->encode($fileExtension);
                    Storage::put($storagePath . $name, $originalImage);
                    
                    //make thumb if not cropped
                    if( ! $image->is_cropped_image ) {
                        $thumbImageContent = Image::make($originalImageContents)
                                                    ->orientate()
                                                    ->encode($fileExtension, 50); 
                        Storage::put($thumbPath . $name, $thumbImageContent);
                        $image->is_cropped_image = 1;
                    } 
                   

                    if( $image->is_cropped_image ) {
                        if( Storage::exists($oldThumbFilePath) ) {
                            $originalThumbImageContents = Storage::get($oldThumbFilePath);
                            $thumbImageContent = Image::make($originalThumbImageContents)
                                                        ->orientate()
                                                        ->encode($fileExtension, 50);                    

                            Storage::put($thumbPath . $name, $thumbImageContent);
                        }
                    }

                    $image->$imageColumn = $name;
                    $image->save();

                    if (!empty($oldImageName)) {
                        $deleteFileList = [
                            $storagePath.$oldImageName,
                            $thumbPath.$oldImageName,
                        ];

                        WagEnabledHelpers::deleteIfFileExist($deleteFileList);
                    }

                    $this->info("----------------------------------");
                    $this->info("[". $recCount ."/$totalimages] Module ID: " . $image->id);
                    $this->info("New File: " . $name . "  <<-------->>  Old File: " . $oldImageName);
                    

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
