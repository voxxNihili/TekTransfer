<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group([
    'prefix'=>'auth'
],function(){
    Route::post('login',[\App\Http\Controllers\AuthController::class,'login']);
    Route::post('register',[\App\Http\Controllers\AuthController::class,'register']);
    Route::post('licenseVerification',[\App\Http\Controllers\AuthController::class,'licenseVerification']);
});

Route::group([
    'middleware'=>['auth:api']
],function(){
    Route::get('payment/iyzipay',[\App\Http\Controllers\api\paymentController::class,'iyzipayPayment']);
    Route::get('report/{code}',[\App\Http\Controllers\api\queryController::class,'reportShow']);
    Route::get('/order/setting/{id}',[\App\Http\Controllers\api\orderController::class,'getSetting']);
    Route::post('/logout',[\App\Http\Controllers\AuthController::class,'logout']);
    Route::post('/authenticate',[\App\Http\Controllers\AuthController::class,'authenticate']);
    Route::post('/order/setting',[\App\Http\Controllers\api\orderController::class,'createSetting']);
    Route::resource('product',\App\Http\Controllers\api\productController::class);
    Route::resource('category',\App\Http\Controllers\api\categoryController::class);
    Route::resource('customer',\App\Http\Controllers\api\customerController::class);
    Route::resource('stock',\App\Http\Controllers\api\stockController::class);
    Route::resource('profile',\App\Http\Controllers\api\profileController::class);
    Route::resource('order',\App\Http\Controllers\api\orderController::class);
    Route::resource('payment',\App\Http\Controllers\api\paymentController::class);
    Route::resource('query',\App\Http\Controllers\api\queryController::class);
    Route::resource('queryParameter',\App\Http\Controllers\api\queryParameterController::class);
    Route::post('/stock/get-customer',[\App\Http\Controllers\api\stockController::class,'getCustomer']);
    Route::post('queryApi/{code}',[\App\Http\Controllers\api\queryController::class,'generateQuery']);

    Route::group(['prefix'=>'home','namespace'=>'home'],function(){
        Route::post('/',[\App\Http\Controllers\api\homeController::class,'index']);
    });
});

Route::group([
    'prefix'=>'web'
],function(){
    Route::get('categoryToProduct',[\App\Http\Controllers\WebController::class,'categoryToProduct']);

});

Route::group([
    'prefix'=>'logo'
],function(){
    Route::post('salesInvoice',[\App\Http\Controllers\LogoSalesController::class,'salesInvoice']);
});
