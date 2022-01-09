<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableServiceTracks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('idService',30);
            $table->string('status',40);
            $table->string('title');
            $table->string('date',50);
            $table->string('time',20);
            $table->string('idWarranty',30)->nullable();
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
        Schema::dropIfExists('service_tracks');
    }
}