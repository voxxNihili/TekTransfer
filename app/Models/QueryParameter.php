<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryParameter extends Model
{
    use HasFactory;
    protected $fillable = ['parameter','name','data_type'];
}
