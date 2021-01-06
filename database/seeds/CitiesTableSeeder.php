<?php

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $sql = public_path('bd_data/cities.sql');
        DB::unprepared(file_get_contents($sql));     
    }
}
