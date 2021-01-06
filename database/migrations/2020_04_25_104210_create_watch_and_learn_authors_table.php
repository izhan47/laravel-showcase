<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchAndLearnAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watch_and_learn_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('website_link')->nullable();
            $table->text('about')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watch_and_learn_author');
    }
}
