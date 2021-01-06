<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchAndLearnDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watch_and_learn_deals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("watch_and_learn_id")->nullable();
            $table->string('deal')->nullable();
            $table->text('fine_print')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'pause'])->default('active');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('watch_and_learn_id')->references('id')->on('watch_and_learn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watch_and_learn_deals');
    }
}
