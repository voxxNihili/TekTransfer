<?php

namespace App\Http\Controllers\api\order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\License;
use App\Models\UserHasRole;
use App\Models\User;
use App\Models\LogoSetting;
use App\Mail\OrderMail;
use Mail;

use Illuminate\Support\Facades\Log;
class orderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();

        if ($userRole->role[0]->code == 'superAdmin') {
            $data = Order::with('license')->get();
        }else {
            $data = Order::where('userId',$user->id)->with('license')->get();
        }

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
        //$user = request()->user();
        $user = User::where('id',$request->userId)->first();
        $all = $request->all();

        $all['userId'] =$user->id;

        $all['orderCode'] = $this->cunique(10);

        $orderLicenseKey = $this->generateKey(20,5);

        $license = License::create([
            'licenseKey'=>$orderLicenseKey
        ]);

        $all['licenseId'] = $license->id;

        $all['price'] = 0;

        $all['productId'] = $request->productId;

        $create = Order::create($all);

        if($create){
            $this->orderMail($user->email,$orderLicenseKey,$create->orderCode);

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

    public function getSetting()
    {
        $order = Order::where('id',request()->id)->first();
        $logoSetting = LogoSetting::where('licenseId',$order->licenseId)->first();

        if($logoSetting){
            return response()->json([
                'success'=>true,
                'setting'=>$logoSetting
            ]);
        }
        else
        {
            return response()->json([
                'success'=>false,
                'message'=>'Lisans Anahtarı Bulunmamaktadır.'
            ]);
        }
    }

    public function createSetting(Request $request)
    {
        $order = Order::where('id',$request->orderId)->first();

        $logoSetting = LogoSetting::where('licenseId',$order->licenseId)->first();


        $all = $request->all();
        unset($all['orderId']);
        $all['licenseId'] =$order->licenseId;
        $all['customerCode'] =$request->customerCode;
        $all['customerType'] =$request->customerType;
        $all['companyId'] =$request->companyId;
        $all['companyName'] =$request->companyName;

        $all['sqlPeriod'] =$request->sqlPeriod;
        $all['sqlCompanyId'] =$request->sqlCompanyId;


        if (!$logoSetting) {
            $create = LogoSetting::create($all);
            if($create){
                return response()->json([
                    'success'=>true,
                    'message'=>'Ayar Kaydedildi.'
                ]);
            }
            else
            {
                return response()->json([
                    'success'=>false,
                    'message'=>'Ayar Kaydedilemedi'
                ]);
            }
        }else{
            $update = LogoSetting::where('id',$logoSetting->id)->update([
                'customerCode'=>$request->customerCode,
                'customerType'=>$request->customerType,
                'companyId'=>$request->companyId,
                'companyName'=>$request->companyName,
                'sqlPeriod'=>$request->sqlPeriod,
                'sqlCompanyId'=>$request->sqlCompanyId
            ]);
            if($update){
                return response()->json([
                    'success'=>true,
                    'message'=>'Ayar Güncellendi.'
                ]);
            }
            else
            {
                return response()->json([
                    'success'=>false,
                    'message'=>'Ayar Güncellenemedi'
                ]);
            }
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
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktadır']);}
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

    public function orderMail($email,$key,$orderCode)
    {
        $user = User::where('email',$email)->first();
        $user = $user->toArray();

        $userEmail = $user["email"];
        $userName = $user["name"];

        $details = [
            'title' => 'Ürün Satın Alma',
            'body' =>'Nolu ürün siparişiniz başarı ile oluşturulmuştur.',
            'name' => $userName,
            'orderCode' => $orderCode,
            'key' => $key
        ];

        Mail::to($userEmail)->send(new OrderMail($details));
        return true;
    }

}
