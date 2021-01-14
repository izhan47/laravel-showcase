<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetCountryStateCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_country_state_city', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pet_pro_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->foreign('pet_pro_id')
            ->references('id')
            ->on('pet_pros')->onDelete('cascade');
      
            $table->foreign('country_id')
                  ->references('id')
                  ->on('countries')->onDelete('cascade');
            $table->foreign('state_id')
                  ->references('id')
                  ->on('states')->onDelete('cascade');
            $table->foreign('city_id')
                  ->references('id')
                  ->on('cities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_country_state_city');
    }
}
