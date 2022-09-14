<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueriesHasParameters extends Model
{
    use HasFactory;
    protected $fillable = ['query_id','parameter_id'];

    public function parameter(){
        return $this->HasMany(QueryParameter::class,'parameter_id','id');
    }
}
