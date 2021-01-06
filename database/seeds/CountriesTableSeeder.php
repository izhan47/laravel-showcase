<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

       $sql = public_path('bd_data/countries.sql');
       DB::unprepared(file_get_contents($sql));
    }

}
