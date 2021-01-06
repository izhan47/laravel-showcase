<?php

namespace App\Console\Commands;

use App\Imports\BreedsImport;
use Illuminate\Console\Command;
use Excel;

class BreedsImportCommand extends Command
{
    protected $signature = 'import:breeds';

    protected $description = 'Import breeds data to db';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Excel::import(new BreedsImport, public_path('breeds/breeds.csv'));
    }
}
