<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $sql = public_path('bd_data/states.sql');
        DB::unprepared(file_get_contents($sql));
    }
}
