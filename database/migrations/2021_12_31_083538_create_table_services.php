<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('kode',30)->nullable();
            $table->string('keluhan');
            $table->string('uangMuka',20)->nullable();
            $table->string('status',30);
            $table->string('estimasiBiaya',20)->nullable();
            $table->string('totalBiaya',30)->nullable();
            $table->string('garansi',20)->nullable();
            $table->bigInteger('idCustomer')->unsigned();
            $table->bigInteger('idProduct')->unsigned();
            $table->boolean('butuhPersetujuan');
            $table->boolean('disetujui')->nullable();
            $table->boolean('konfirmasiBiaya');
            $table->boolean('diambil');
            $table->dateTime('waktuMasuk');
            $table->dateTime('waktuAmbil')->nullable();
            $table->string('usernameCS',30);
            $table->string('usernameTeknisi',30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}