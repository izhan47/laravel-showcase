<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProSelectedBusinessNaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_selected_business_natures', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger("pet_pro_id");
			$table->unsignedBigInteger("business_id");
			$table->timestamps();
			
			$table->foreign('pet_pro_id')->references('id')->on('pet_pros')->onDelete('cascade');
			$table->foreign('business_id')->references('id')->on('business_natures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_pro_selected_business_natures');
    }
}
