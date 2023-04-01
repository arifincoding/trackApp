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
            $table->string('code', 30)->nullable();
            $table->string('complaint');
            $table->unsignedInteger('down_payment')->default(0);
            $table->string('status', 30)->default('antri');
            $table->unsignedInteger('estimated_cost')->default(0);
            $table->unsignedInteger('total_cost')->default(0);
            $table->string('warranty', 20)->nullable();
            $table->foreignId('product_id')->constrained();
            $table->boolean('need_approval');
            $table->boolean('is_approved')->nullable();
            $table->boolean('is_cost_confirmation')->default(false);
            $table->boolean('is_take')->default(false);
            $table->timestamp('entry_at')->useCurrent();
            $table->timestamp('taked_at')->nullable();
            $table->string('cs_username', 70);
            $table->string('tecnician_username', 70)->nullable();
            $table->string('note')->nullable();
            $table->foreign('cs_username')->references('username')->on('users');
            $table->foreign('tecnician_username')->references('username')->on('users');
            $table->fulltext('complaint');
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
