<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventEndDateToPetProEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pet_pro_events', function (Blueprint $table) {
            $table->date('event_end_date')->nullable()->after('event_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pet_pro_events', function (Blueprint $table) {
            $table->dropColumn('event_end_date');
        });
    }
}
