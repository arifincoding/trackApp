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
            $table->string('username', 70)->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('firstname', 70);
            $table->string('lastname', 70);
            $table->unsignedDecimal('telp', 14, 0)->nullable();
            $table->enum('role', ['pemilik', 'teknisi', 'customer service']);
            $table->enum('gender', ['pria', 'wanita']);
            $table->string('address')->nullable();
            $table->string('email')->unique();
            $table->fulltext(['firstname','lastname']);
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