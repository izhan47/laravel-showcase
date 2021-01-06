<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string("email")->unique();
            $table->string('password');  
            $table->string("phone_number")->nullable();
            $table->enum('supper_admin', ["1", "0"])->default("0");
            $table->timestamps();
        });

        $insertArr = [
            [ "email" => "admin@wagenabled.com", "password" => \Hash::make("123456"), "supper_admin" => "1"],
        ];

        DB::table('admin_users')->insert($insertArr);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
}
