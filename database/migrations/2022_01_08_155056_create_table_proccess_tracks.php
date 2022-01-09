<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProccessTracks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proccess_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('idDiagnosa',30);
            $table->string('status',40);
            $table->string('title');
            $table->string('date',50);
            $table->string('time',20);
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
        Schema::dropIfExists('proccess_tracks');
    }
}