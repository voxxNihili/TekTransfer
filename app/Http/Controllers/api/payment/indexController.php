<?php

namespace App\Http\Controllers\api\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Payment;

use Illuminate\Support\Facades\Log;
class indexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $user = request()->user();
        $data = Payment::where('userId',$user->id)->get();

        return response()->json(['success'=>true,'user'=>$user,'data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){}

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
        $all['userId'] = $user->id;

        $create = Payment::create($all);
        if ($create) {
            $this->paymentMail($user->email);
        }
        
        if($create){
            return response()->json([
                'success'=>true,
                'message'=>'Sipariş Oluşturuldu'
            ]);
        }
        else 
        {
            return response()->json([
                'success'=>false,
                'message'=>'Sipariş Oluşturulamadı'
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = request()->user();
        $control = Product::where('id',$id)->where('userId',$user->id)->count();
        if($control == 0){ return response()->json(['success'=>false,'message'=>'Ürün size ait degil']);}
        foreach( ProductImage::where('productId',$id)->get() as $item){
            try { unlink(public_path($item->path)); } catch(\Exception $e){}
        }
        ProductImage::where('productId',$id)->delete();
        ProductProperty::where('productId',$id)->delete();
        Product::where('id',$id)->delete();
        return response()->json(['success'=>true,'message'=>'Silindi']);
    }

    public function getRandChar()
    {
        $ignore_list = ['1', 'I', '0', 'O', 'W', 'Q', 'X', 'S', '5', 'J'];

        if (rand(0, 3)) {
            $char = chr(65 + rand(0, 25));
        } else {
            $char = rand(0, 9);
        }

        if (in_array($char, $ignore_list)) {
            $char = $this->getRandChar();
        }

        return $char;
    }

    public function cunique($length)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $this->getRandChar();
        }
        return $code;
    }

    public function generateKey($length,$part)
    {
        $key = '';

        $end = $length / $part;

        for ($i=0; $i < $end; $i++) { 
            for ($j=0; $j < $part; $j++) { 
                $key .= $this->getRandChar();
            }
            if ($i != $end-1) {
                $key = $key.'-';
            }
        }
        return $key;
    }

    public function paymentMail($email)
    {
        $user = User::where('email',$email)->first();
        $user = $user->toArray();
        
        $userEmail = $user["email"];
        $userName = $user["name"];

        $details = [
            'title' => 'Ödeme',
            'body' =>'Ödemeniz başarı ile gerçekleşmiştir.',
            'name' => $userName
        ];

        Mail::to($userEmail)->send(new PaymentMail($details));
        return true;
    }

}
