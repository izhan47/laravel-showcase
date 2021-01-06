<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetProDealClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_pro_deal_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pet_pro_deal_id")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->timestamps();

            $table->foreign('pet_pro_deal_id')->references('id')->on('pet_pro_deals');
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
        Schema::dropIfExists('pet_pro_deal_claims');
    }
}
