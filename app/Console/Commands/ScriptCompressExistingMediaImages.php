<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Http\WagEnabledHelpers;

class ScriptCompressExistingMediaImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:compress-existing-media-images {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script for compress existing media images';

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

       
        if($modyleType == "watch-and-learn-media") {
            $classObj = app()->make("App\Models\WatchAndLearnMedias");
            $storagePath = config('wagenabled.path.doc.watch_and_learn_media_path');
            $imageColumn = "filename";
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


        $this->info("===>>>> Total Records: " . $images->count());
        $this->info("*************************************************");
        
        $totalimages = $images->count();
        if($totalimages) {
            $thumbPath = $storagePath . "thumb/";            
            $recCount = 0;

            foreach ($images as $image) {
                try {
                    $recCount++;
                   
                    $oldImageName = $image->$imageColumn;    
                    $filePathInfo = pathinfo($oldImageName);
                    $fileExtension = (isset($filePathInfo['extension']) && !empty($filePathInfo['extension'])) ? $filePathInfo['extension'] : "jpg" ;
                                                    
                    $oldFilePath = $storagePath . $oldImageName;
                    $oldThumbFilePath = $thumbPath . $oldImageName;  
                    if( Storage::exists($oldFilePath) ) {                    

                        $imageSize = Storage::size($oldFilePath);

                        if( $imageSize > 100000 ) {

                            $originalImageContents = Storage::get($oldFilePath);
                            $originalImage = Image::make($originalImageContents)
                                                        ->orientate()
                                                        ->encode($fileExtension, 75);
                            Storage::put($storagePath . $image->$imageColumn, $originalImage);
                                              
                            $thumbImageContent = Image::make($originalImageContents)
                                                        ->orientate()
                                                        ->resize(500, null, function ($constraints) {
                                                            $constraints->aspectRatio();
                                                        })
                                                        ->encode($fileExtension, 75);                
                            Storage::put($thumbPath . $image->$imageColumn, $thumbImageContent);                    
                          
                        
                            $this->info("----------------------------------");
                            $this->info("[". $recCount ."/$totalimages] Module ID: " . $image->id);
                            $this->info("New File: " . $image->$imageColumn . "  <<-------->>  Old File: " . $oldImageName);
                        }
                    }


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
