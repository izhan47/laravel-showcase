<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToWatchAndLearnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watch_and_learn', function (Blueprint $table) {
            $table->enum('status', ["draft", "published"])->default("draft")->after('alt_image_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('watch_and_learn', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
