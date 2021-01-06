<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchAndLearnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watch_and_learn', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("category_id")->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('blog_meta_description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('video_type', ['embed_link', 'video_upload'])->nullable();
            $table->string('video_file')->nullable();
            $table->string('embed_link')->nullable();
            $table->string('duration')->nullable();
            $table->string('alt_image_text')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('watch_and_learn_categories');
            $table->foreign('author_id')->references('id')->on('watch_and_learn_authors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watch_and_learn');
    }
}
