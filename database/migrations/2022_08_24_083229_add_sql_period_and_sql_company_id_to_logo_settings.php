<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSqlPeriodAndSqlCompanyIdToLogoSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logo_settings', function (Blueprint $table) {
            $table->string('sqlPeriod')->nullable();
            $table->string('sqlCompanyId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logo_settings', function (Blueprint $table) {
            $table->string('sqlPeriod')->nullable();
            $table->string('sqlCompanyId')->nullable();
        });
    }
}
