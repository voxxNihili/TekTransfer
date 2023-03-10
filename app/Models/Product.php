<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded  = [];

    public function property(){
        return $this->HasMany(ProductProperty::class,'productId','id');
    }

    public function images(){
        return $this->HasMany(ProductImage::class,'productId','id');
    }

    public function category(){
        return $this->HasMany(Category::class,'id','categoryId');
    }

    public function price($pId,$uLId,$mLID){
        $productPrice = new ProductPrice;
        $productPrice = $productPrice->where('productId', $pId)->where('userLimitId', $uLId)->where('monthLimitId', $mLID)->first();
        if ($productPrice) {
            $price = $productPrice->price;
        }else {
            $price = 0;
        }
        return $price;
    }
}
