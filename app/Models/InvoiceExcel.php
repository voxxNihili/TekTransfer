<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceExcel extends Model
{
    use HasFactory;
    protected $fillable = ['turu',
    'tarih',
    'vade_tarih',
    'durumu',
    'evrak_turu',
    'evrak_numarasi',
    'cari_adi',
    'vergi_numarasi',
    'vergi_dairesi',
    'cari_adresi',
    'il',
    'ilce',
    'e_mail',
    'stok_no',
    'uretici',
    'stok_adi',
    'urun_aaiklama',
    'raf',
    'kdv_orani',
    'miktar',
    'iskonto_oran',
    'birim',
    'fiyat',
    'iskonto',
    'ara_toplam',
    'kdv_toplami',
    'yekun',
    'plaka',
    'km',
    'sase_no',
    'ozel_kod',
    'aciklama',
    'depo_kodu',
    'sube',
    'satis_tipi',
    'hesap'];
}