<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogoPaymentRequest extends Model
{
    use HasFactory;
    protected $fillable = ['request_data','ip','licenseKey','company_id','type','payment_date','current_id','price','status','response_message'];
}