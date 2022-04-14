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
            $table->string('username',50)->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('namaDepan',30);
            $table->string('namaBelakang',50);
            $table->string('noHp',20)->nullable();
            $table->string('peran',20);
            $table->string('jenisKelamin',15);
            $table->string('alamat')->nullable();
            $table->string('profilPic')->nullable();
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