<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username',50)->unique();
            $table->string('password');
            $table->string('firstName',30);
            $table->string('lastName',50);
            $table->string('phoneNumber',20)->nullable();
            $table->string('role',20);
            $table->string('gender',15);
            $table->string('address')->nullable();
            $table->string('profilPic')->nullable();
            $table->string('status');
            $table->string('email')->unique();
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
        Schema::dropIfExists('users');
    }
}