<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;
    protected $fillable = ['name','code','sqlQuery'];

    public function queryParam(){
        return $this->HasMany(QueriesHasParameters::class,'query_id','id');
    }
}
