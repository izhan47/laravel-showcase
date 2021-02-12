<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchAndLearnSelectedCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watch_and_learn_selected_categories', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger("watch_and_learn_id");
			$table->unsignedBigInteger("selected_category_id");
			$table->timestamps();
			
			$table->foreign('watch_and_learn_id')->references('id')->on('watch_and_learn')->onDelete('cascade');
			$table->foreign('selected_category_id')->references('id')->on('watch_and_learn_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watch_and_learn_selected_categories');
    }
}
