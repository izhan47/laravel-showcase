<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPetBreedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_pet_breeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("users_pet_id")->nullable();
            $table->unsignedBigInteger("breed_id")->nullable();
            $table->timestamps();

            $table->foreign('users_pet_id')->references('id')->on('user_pets');
            $table->foreign('breed_id')->references('id')->on('breeds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_pet_breeds');
    }
}
