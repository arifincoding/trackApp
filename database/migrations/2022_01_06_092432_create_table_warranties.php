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
            $table->string('completeness',60)->nullable();
            $table->string('complaint');
            $table->string('productDefects')->nullable();
            $table->string('entryDate',20);
            $table->string('entryTime',10);
            $table->string('pickDate',20)->nullable();
            $table->string('pickTime',10)->nullable();
            $table->string('note',100)->nullable();
            $table->string('csName',70);
            $table->string('technicianName',70)->nullable();
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