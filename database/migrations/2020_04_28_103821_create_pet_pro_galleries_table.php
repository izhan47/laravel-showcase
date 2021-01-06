<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pet_pro_id")->nullable();
            $table->string("gallery_image")->nullable();
            $table->boolean('is_cover_image')->default(0);
            $table->timestamps();

            $table->foreign('pet_pro_id')->references('id')->on('pet_pros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_pro_galleries');
    }
}
