<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCardInfosToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('card')->nullable();
            $table->string('errorCode')->nullable();
            $table->string('cardTokens')->nullable();
            $table->string('transactionId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('card')->nullable();
            $table->string('errorCode')->nullable();
            $table->string('cardTokens')->nullable();
            $table->string('transactionId')->nullable();
        });
    }
}
