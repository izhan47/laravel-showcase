<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_deals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pet_pro_id")->nullable();
            $table->string('deal')->nullable();
            $table->text('fine_print')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'pause'])->default('active');
            $table->softDeletes();
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
        Schema::dropIfExists('pet_pro_deals');
    }
}
