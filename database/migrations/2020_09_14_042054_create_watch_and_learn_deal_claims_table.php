<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchAndLearnDealClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watch_and_learn_deal_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("watch_and_learn_deal_id")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->timestamps();

            $table->foreign('watch_and_learn_deal_id')->references('id')->on('watch_and_learn_deals');
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
        Schema::dropIfExists('watch_and_learn_deal_claims');
    }
}
