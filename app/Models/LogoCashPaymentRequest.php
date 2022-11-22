<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogoCashPaymentRequest extends Model
{
    use HasFactory;
    protected $fillable = ['request_data','ip','licenseKey','company_id','type','description','payment_date','current_id','price','status','response_message'];
}