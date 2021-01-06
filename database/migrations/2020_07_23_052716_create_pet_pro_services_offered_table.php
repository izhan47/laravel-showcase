<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProServicesOfferedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_services_offered', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pet_pro_id");
            $table->string("service")->nullable();
            $table->timestamps();
            
            $table->foreign('pet_pro_id')->references('id')->on('pet_pros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_pro_services_offered');
    }
}
