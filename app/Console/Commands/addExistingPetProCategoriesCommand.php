<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PetPro;
use App\Models\PetProSelectedCategory;
use Carbon\Carbon;

class addExistingPetProCategoriesCommand extends Command
{
    protected $signature = 'add:petprocategory';

    protected $description = 'add existing pet pro categories in new table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
		$currentTime = Carbon::now();  
		$petPros = PetPro::select(['id', 'category_id'])->get();
		foreach ($petPros as $key => $petPro) {
			PetProSelectedCategory::create([
				"pet_pro_id" => $petPro->id,
				"category_id" => $petPro->category_id,
				"created_at" => $currentTime,
				"updated_at" => $currentTime,
			]);
		}
    }
}
