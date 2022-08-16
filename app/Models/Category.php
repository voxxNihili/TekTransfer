<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['userId','name'];

    public function categoryToProduct(){
        return $this->HasMany(Product::class,'categoryId','id');
    }
}
