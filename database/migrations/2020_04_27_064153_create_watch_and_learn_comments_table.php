<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchAndLearnCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watch_and_learn_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("watch_and_learn_id")->nullable();
            $table->integer('parent_comment_id')->default(0);
            $table->unsignedBigInteger("user_id")->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('watch_and_learn_id')->references('id')->on('watch_and_learn');
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
        Schema::dropIfExists('watch_and_learn_comments');
    }
}
