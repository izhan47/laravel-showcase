<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pet_pro_id")->nullable();
            $table->string('name')->nullable();
            $table->date('event_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('address')->nullable();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('pet_pro_events');
    }
}
