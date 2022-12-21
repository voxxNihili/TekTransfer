<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceExcels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_excels', function (Blueprint $table) {
            $table->id();
            $table->string('turu')->nullable();
            $table->string('tarih')->nullable();
            $table->string('vade_tarih')->nullable();
            $table->string('durumu')->nullable();
            $table->string('evrak_turu')->nullable();
            $table->string('evrak_numarasi')->nullable();
            $table->string('cari_adi')->nullable();
            $table->string('vergi_numarasi')->nullable();
            $table->string('vergi_dairesi')->nullable();
            $table->string('cari_adresi')->nullable();
            $table->string('il')->nullable();
            $table->string('ilce')->nullable();
            $table->string('e_mail')->nullable();
            $table->string('stok_no')->nullable();
            $table->string('uretici')->nullable();
            $table->string('stok_adi')->nullable();
            $table->string('urun_aaiklama')->nullable();
            $table->string('raf')->nullable();
            $table->string('kdv_orani')->nullable();
            $table->string('miktar')->nullable();
            $table->string('iskonto_oran')->nullable();
            $table->string('birim')->nullable();
            $table->string('fiyat')->nullable();
            $table->string('iskonto')->nullable();
            $table->string('ara_toplam')->nullable();
            $table->string('kdv_toplami')->nullable();
            $table->string('yekun')->nullable();
            $table->string('plaka')->nullable();
            $table->string('km')->nullable();
            $table->string('sase_no')->nullable();
            $table->string('ozel_kod')->nullable();
            $table->string('aciklama')->nullable();
            $table->string('depo_kodu')->nullable();
            $table->string('sube')->nullable();
            $table->string('satis_tipi')->nullable();
            $table->string('hesap')->nullable();
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
        Schema::dropIfExists('invoice_excels');
    }
}

