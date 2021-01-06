<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;

class UpdateValidCitiesFlagCommand extends Command
{
    protected $signature = 'update:cities';

    protected $description = 'update valid city flag to 1';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
		$model = new City;

        $model->setConnection('mysql2');

        $citiesFromNextplay = $model->pluck('name')->toArray();
		
		$model->setConnection('mysql');
		
		$cities = $model->whereIn('name', $citiesFromNextplay)->update([
			'is_valid' => 1
		]);
    }
}
