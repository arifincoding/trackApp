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
            $table->string('code',30)->nullable();
            $table->string('name',60);
            $table->string('category',30);
            $table->string('complaint');
            $table->string('productDefects')->nullable();
            $table->string('completeness',60)->nullable();
            $table->string('note',100)->nullable();
            $table->string('downPayment',20)->nullable();
            $table->string('status',30);
            $table->string('estimatePrice',20)->nullable();
            $table->string('price',20)->nullable();
            $table->string('totalPrice',30)->nullable();
            $table->string('warranty',20)->nullable();
            $table->string('idCustomer',20);
            $table->boolean('needConfirm');
            $table->boolean('confirmed');
            $table->boolean('confirmCost');
            $table->boolean('picked');
            $table->string('entryDate',20);
            $table->string('pickDate',20)->nullable();
            $table->string('entryTime',10);
            $table->string('pickTime',10)->nullable();
            $table->string('csUserName',30);
            $table->string('technicianUserName',70)->nullable();
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