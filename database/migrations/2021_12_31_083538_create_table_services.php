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
            $table->string('nama',60);
            $table->string('kategori',30);
            $table->string('keluhan');
            $table->string('cacatProduk')->nullable();
            $table->string('kelengkapan',60)->nullable();
            $table->string('catatan',100)->nullable();
            $table->string('uangMuka',20)->nullable();
            $table->string('status',30);
            $table->string('estimasiBiaya',20)->nullable();
            $table->string('biaya',20)->nullable();
            $table->string('totalBiaya',30)->nullable();
            $table->string('garansi',20)->nullable();
            $table->string('idCustomer',20);
            $table->boolean('butuhKonfirmasi');
            $table->boolean('dikonfirmasi')->nullable();
            $table->boolean('konfirmasiBiaya');
            $table->boolean('diambil');
            $table->string('tanggalMasuk',20);
            $table->string('tanggalAmbil',20)->nullable();
            $table->string('jamMasuk',10);
            $table->string('jamAmbil',10)->nullable();
            $table->string('usernameCS',30);
            $table->string('usernameTeknisi',30)->nullable();
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
        Schema::dropIfExists('services');
    }
}