<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pet_pro_id")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->string('name')->nullable();
            $table->float('rate', 8,2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('pet_pro_id')->references('id')->on('pet_pros');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_pro_reviews');
    }
}
