<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProSelectedCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_selected_categories', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("pet_pro_id");
			$table->unsignedBigInteger("category_id");
			$table->timestamps();
			
			$table->foreign('pet_pro_id')->references('id')->on('pet_pros')->onDelete('cascade');
			$table->foreign('category_id')->references('id')->on('pet_pro_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_pro_selected_categories');
    }
}
