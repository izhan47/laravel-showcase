<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUserToTablePetPros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pet_pros', function (Blueprint $table) {
            $table->unsignedBigInteger("user_id")->nullable();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string("user_type")->nullable();
            $table->string("status")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('pet_pros', function (Blueprint $table) {
            $table->dropColumn("user_id");
			$table->dropColumn("user_type");
            $table->dropColumn("status");
        });
        Schema::enableForeignKeyConstraints();
    }
}
