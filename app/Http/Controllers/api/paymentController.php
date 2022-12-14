<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Payment;
use App\Models\UserHasRole;
use App\Models\User;
use App\Models\LogoPaymentRequest;
use App\Models\LogoCashPaymentRequest;
use Illuminate\Support\Facades\Log;
class paymentController extends Controller
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


    public function logoCreditCardPaymentList()
    {
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();

        if ($userRole->role[0]->code == 'superAdmin') {
            $data = LogoPaymentRequest::orderBy('id','desc')->get();
        }

        return response()->json(['success'=>true,'user'=>$user,'data'=>$data]);
    }

    public function logoCashPaymentList()
    {
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();

        if ($userRole->role[0]->code == 'superAdmin') {
            $data = LogoCashPaymentRequest::orderBy('id','desc')->get();
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

    public function iyzipayPayment($product)
    {   

        $ProductItem = Product::where('id',$product)->first();

        $options = new \Iyzipay\Options();
        $options->setApiKey("u8Dako7Qn4PJrtwx8M8lxfknmEezNgkK");
        $options->setSecretKey("6QYF2K0jgUadBUCrH6CzbuG4ZdYtYUMI");
        $options->setBaseUrl("https://api.iyzipay.com");

        # create request class
        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice($ProductItem->sellingPrice);
        $request->setPaidPrice($ProductItem->sellingPrice);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId("B67832");
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://www.merchant.com/callback");
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");
        $buyer->setName("John");
        $buyer->setSurname("Doe");
        $buyer->setGsmNumber("+905350000000");
        $buyer->setEmail("email@email.com");
        $buyer->setIdentityNumber("74300864791");
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $buyer->setIp("85.34.78.112");
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName("Jane Doe");
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);

        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Binocular");
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setCategory2("Accessories");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice($ProductItem->sellingPrice);
        $basketItems[0] = $firstBasketItem;

        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
        $checkout_form = $checkoutFormInitialize->getCheckoutFormContent();

        return $checkout_form;
    }


}
