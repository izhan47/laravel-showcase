<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableWatchAndLearnChangeBlogMetaDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watch_and_learn', function (Blueprint $table) {
            $table->text('blog_meta_description')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('watch_and_learn', function (Blueprint $table) {
            $table->string('blog_meta_description')->nullable()->change();
        });
    }
}
