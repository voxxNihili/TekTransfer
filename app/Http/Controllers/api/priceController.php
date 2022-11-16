<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductImage;
use App\Models\ProductProperty;
use App\Models\ProductMonthRange;
use App\Models\ProductUserNumber;
use App\Models\UserHasRole;
use App\Models\Role;
use App\Helper\fileUpload;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
class priceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $oldProductPrice = new ProductPrice;
        $oldProductPrice = $oldProductPrice->where('productId',$request->productId)->delete();
        if($request->prices){
            try {
                foreach ($request->prices as $param) {
                    $productPrice = new ProductPrice;
                    $productPrice->productId = $request->productId;
                    $productPrice->userLimitId = $param["userLimitId"];
                    $productPrice->monthLimitId = $param["monthLimitId"];
                    $productPrice->price = $param["price"];
                    $productPrice->save();
                }
                return response()->json([
                    'success'=>true,
                    'message'=>'Ürün Fiyatları Oluşturuldu'
                ],201);
            } catch (\Throwable $th) {
                return response()->json([
                    'success'=>false,
                    'message'=>'Ürün Fiyatı Oluşturulamadı'.$th
                ],500);
            }
        }
        else{
            return response()->json([
                'success'=>false,
                'message'=>'Geçersiz İstek'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productPrice = new ProductPrice;
        $productPrice = $productPrice->where('productId',$id)->get();
        return response()->json(['success'=>true, 'data'=>$productPrice]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

}
