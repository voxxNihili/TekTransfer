<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditCardRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_card_requests', function (Blueprint $table) {
            $table->id();
            $table->text('request_data')->nullable();
            $table->string('ip')->nullable();
            $table->string('licenseKey')->nullable();
            $table->integer('company_id')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('current_id')->nullable();
            $table->double('price')->nullable();
            $table->string('status')->nullable();
            $table->string('response_message')->nullable();
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
        Schema::dropIfExists('credit_card_requests');
    }
}
