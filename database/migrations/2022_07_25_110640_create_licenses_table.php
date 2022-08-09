<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('licenseKey')->nullable();
            $table->string('ip')->nullable();
            $table->string('pcName')->nullable();
            $table->string('osVersion')->nullable();
            $table->string('macAddress')->nullable();
            $table->string('macAddress2')->nullable();
            $table->string('startDate')->nullable();
            $table->string('endDate')->nullable();
            $table->integer('accountLimit')->nullable();
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
        Schema::dropIfExists('licenses');
    }
}
