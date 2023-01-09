<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogoExcelRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logo_excel_requests', function (Blueprint $table) {
            $table->id();
            $table->text('request_data')->nullable();
            $table->string('type')->nullable();
            $table->string('ip')->nullable();
            $table->dateTime('invoice_date')->nullable();
            $table->string('current')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('status')->nullable();
            $table->string('return')->nullable();
            $table->string('response_message')->nullable();
            $table->string('invoice_status')->nullable();
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
        Schema::dropIfExists('logo_excel_requests');
    }
}
