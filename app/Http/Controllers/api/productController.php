<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductProperty;
use App\Models\ProductMonthRange;
use App\Models\ProductUserNumber;
use App\Models\UserHasRole;
use App\Models\Role;
use App\Helper\fileUpload;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
class productController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = request()->user();
        $data = Product::with('category')->get();
        return response()->json(['success'=>true,'user'=>$user,'data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = request()->user();
        $categories = Category::where('userId',$user->id)->get();
        return response()->json([
            'success'=>true,
            'categories'=>$categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = request()->user();
        $all = $request->all();
        $file = (isset($all['file'])) ? $all['file'] : [];
        $properties = (isset($all['property'])) ? json_decode($all['property'],true) : [];
        unset($all['file']);
        unset($all['property']);
        $all['userId'] = $user->id;
        $create = Product::create($all);
        if($create){
            foreach($file as $item){

                $upload = fileUpload::newUpload(rand(1,9000),"products",$item,0);

                ProductImage::create([
                    'productId'=>$create->id,
                    'path'=>$upload
                ]);
            }
            foreach($properties as $property){
                ProductProperty::create([
                    'productId'=>$create->id,
                    'property'=>$property['property'],
                    'value'=>$property['value']
                ]);
            }
            return response()->json([
                'success'=>true,
                'message'=>'??r??n Ba??ar?? ile Eklendi'
            ]);
        }
        else
        {
            return response()->json([
                'success'=>false,
                'message'=>'??r??n Eklenemedi'
            ]);
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
        $product = Product::where('id',$id)->with('property')->with('images')->first();
        return response()->json([
            'success'=>true,
            'product'=>$product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktad??r']);}
        $product = Product::where('id',$id)->with('property')->with('images')->first();
        $categories = Category::all();
        return response()->json([
            'success'=>true,
            'categories'=>$categories,
            'product'=>$product
        ]);
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
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktad??r']);}
        $all = $request->all();
        $file = (isset($all['file'])) ? json_decode($all['file'],true) : [];
        $newFile = (isset($all['newFile'])) ? $all['newFile'] : [];
        $properties = (isset($all['property'])) ? json_decode($all['property'],true) : [];
        Log::info(json_encode($file));
        foreach($file as $item){
            if(isset($item['isRemove'])){
                $productImage = ProductImage::where('id',$item['id'])->first();
                try {
                    unlink(public_path($productImage->image));
                }
                catch(\Exception $e){

                }
                ProductImage::where('id',$item['id'])->delete();
            }
        }

        foreach($newFile as $item){

            $upload = fileUpload::newUpload(rand(1,9000),"products",$item,0);
            ProductImage::create([
                'productId'=>$id,
                'path'=>$upload
            ]);
        }

        ProductProperty::where('productId',$id)->delete();
        foreach($properties as $property){
            ProductProperty::create([
                'productId'=>$id,
                'property'=>$property['property'],
                'value'=>$property['value']
            ]);
        }


        unset($all['file']);
        unset($all['newFile']);
        unset($all['_method']);
        unset($all['property']);
        $create = Product::where('id',$id)->update($all);
        if($create){

            return response()->json([
                'success'=>true,
                'message'=>'??r??n D??zenleme Ba??ar??l??'
            ]);
        }
        else
        {
            return response()->json([
                'success'=>false,
                'message'=>'??r??n Eklenemedi'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = request()->user();      
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktad??r']);}
        foreach( ProductImage::where('productId',$id)->get() as $item){
            try { unlink(public_path($item->path)); } catch(\Exception $e){}
        }
        ProductImage::where('productId',$id)->delete();
        ProductProperty::where('productId',$id)->delete();
        Product::where('id',$id)->delete();
        return response()->json(['success'=>true,'message'=>'Silindi']);
    }

    public function getProductMonthNumber(){
        $data = ProductMonthRange::get();
        return response()->json(['success'=>true,'data'=>$data]);
    }

    public function getProductUserNumber(){
        $data = ProductUserNumber::get();
        return response()->json(['success'=>true,'data'=>$data]);
    }
}
