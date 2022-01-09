<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDiagnosas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnosas', function (Blueprint $table) {
            $table->id();
            $table->string('title',100);
            $table->string('idService',30)->nullable();
            $table->boolean('confirmed')->nullable();
            $table->string('status',30)->nullable();
            $table->string('price',20)->nullable();
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
        Schema::dropIfExists('diagnosas');
    }
}