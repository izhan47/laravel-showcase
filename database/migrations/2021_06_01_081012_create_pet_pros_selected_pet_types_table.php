<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProsSelectedPetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pros_selected_pet_types', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger("pet_pro_id");
			$table->unsignedBigInteger("pet_type_id");
			$table->timestamps();
			
			$table->foreign('pet_pro_id')->references('id')->on('pet_pros')->onDelete('cascade');
			$table->foreign('pet_type_id')->references('id')->on('pet_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_pros_selected_pet_types');
    }
}
