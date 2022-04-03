<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableWarranties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->string('idService',30);
            $table->string('kelengkapan',60)->nullable();
            $table->string('keluhan');
            $table->string('cacatProduk')->nullable();
            $table->string('tanggalMasuk',20);
            $table->string('jamMasuk',10);
            $table->string('tanggalAmbil',20)->nullable();
            $table->string('jamAmbil',10)->nullable();
            $table->string('catatan',100)->nullable();
            $table->string('usernameCS',70);
            $table->string('usernameTeknisi',70)->nullable();
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
        Schema::dropIfExists('warranties');
    }
}