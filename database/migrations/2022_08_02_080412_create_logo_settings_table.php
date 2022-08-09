<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogoSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logo_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('licenseId')->nullable();
            $table->string('customerCode')->nullable();
            $table->integer('customerType')->nullable();
            $table->integer('companyId')->nullable();
            $table->string('companyName')->nullable();
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
        Schema::dropIfExists('logo_settings');
    }
}
