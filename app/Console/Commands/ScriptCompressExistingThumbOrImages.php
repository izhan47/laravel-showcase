<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Http\WagEnabledHelpers;

class ScriptCompressExistingThumbOrImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:compress-existing-thumb-or-images {--type=}';

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

        if($modyleType == "watch-and-learn-author") {
            $classObj = app()->make("App\Models\WatchAndLearnAuthor");
            $storagePath = config('wagenabled.path.doc.watch_and_learn_author_path');
            $imageColumn = "profile_image";
            $width = 380;
            $height = 250;
        }   
        else if($modyleType == "watch-and-learn") {
            $classObj = app()->make("App\Models\WatchAndLearn");
            $storagePath = config('wagenabled.path.doc.watch_and_learn_thumbnail_path');
            $imageColumn = "thumbnail";
            $width = 380;
            $height = 250;
        }     
        else if($modyleType == "testimonial") {
            $classObj = app()->make("App\Models\Testimonial");
            $storagePath = config('wagenabled.path.doc.testimonial_image_path');
            $imageColumn = "image";
            $width = 300;
            $height = 350;
        }         
        else {
            $this->error("Invalid Type, Please double check it");
            return false;
        }

        $this->compressModuleWiseImages($classObj, $storagePath, $imageColumn, $width, $height);
    
        $this->info("*************************************");
        $this->info("Process Completed....");
        $this->info("*************************************");

    }

    protected function compressModuleWiseImages($model, $storagePath, $imageColumn = "image", $width, $height)
    {
        $images = $model->select(["id", $imageColumn])
                        ->whereNotNull($imageColumn)
                        ->limit(4)
                        ->get();


        $this->info("===>>>> Total Records: " . $images->count());
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

                    $originalThumbImageContents = Storage::get($oldThumbFilePath);
                    $oldThumbImageContent = Image::make($originalThumbImageContents);
                    
                        /*$this->info("===>>>> height: ".$oldThumbImageContent->height());
                        $this->info("===>>>> width: ".$oldThumbImageContent->width() );*/

                    if( $oldThumbImageContent->height() > $height || $oldThumbImageContent->width() > $width ) {
                        // image is cropped
                        $thumbImageContent = Image::make($originalThumbImageContents)
                                                    ->orientate()
                                                    ->encode($fileExtension, 50);                    

                        Storage::put($thumbPath . $name, $thumbImageContent);
                        $this->info("===>>>> not cropped: " );
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
