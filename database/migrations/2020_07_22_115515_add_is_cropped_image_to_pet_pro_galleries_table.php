<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCroppedImageToPetProGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pet_pro_galleries', function (Blueprint $table) {
            $table->boolean('is_cropped_image')->default(1)->after('is_cover_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pet_pro_galleries', function (Blueprint $table) {
            $table->dropColumn('is_cropped_image');
        });
    }
}
