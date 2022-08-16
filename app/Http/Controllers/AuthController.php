<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\License;
use App\Models\Order;
use App\Mail\SendMail;
use App\Models\UserHasRole;
use Mail;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
class AuthController extends Controller
{

    public function licenseVerification(Request $requestData){

        $requestDecrypted = $this->requestDecrypted($requestData->data);
        if (!$requestDecrypted) {
            return response()->json(['success'=>false,'message'=>'Geçersiz İstek'],201);
        }
        $request = json_decode($requestDecrypted);
        $license = License::where('licenseKey',$request->licenseKey)->first();

        if ($license) {
            $order = Order::where('licenseId',$license->id)->first();
            $startDate = $order->created_at->addHours(3);
            $endDate = $startDate->addDays(15);
            if ($license->pcName == null) {
                try {
                    $update = License::where('licenseKey',$request->licenseKey)->update([
                        'ip'=>$request->ip,
                        'pcName'=>$request->pcName,
                        'osVersion'=>$request->osVersion,
                        'macAddress'=>$request->macAddress,
                        'macAddress2'=>$request->macAddress2,
                        'startDate'=>$startDate,
                        'endDate'=>$endDate
                    ]);

                    if($update){
                        return response()->json([
                            'success'=>true,
                            'licenseDate'=>$this->requestEncrypted($endDate),
                            'dateTimer'=>$this->requestEncrypted($request->dateTimer),
                            'message'=>'Lisansınız, giriş yaptığınız bilgisayara tanımlanmıştır.'
                        ],200);
                    }
                    else
                    {
                        return response()->json(['success'=>false,'message'=>'Lisansınız aktifleştirilemedi'],201);
                    }

                } catch (\Throwable $th) {
                    //throw $th;
                }

            }else {

                if ($license->pcName == $request->pcName && $license->ip == $request->ip && $license->osVersion == $request->osVersion && $license->macAddress == $request->macAddress) {

                    $updatePort = License::where('licenseKey',$request->licenseKey)->update([
                        'port'=>$request->port
                    ]);

                    if($updatePort){
                        return response()->json([
                            'success'=> true,
                            'licenseDate'=> $this->requestEncrypted($endDate),
                            'dateTimer'=> $this->requestEncrypted($request->dateTimer),
                            'message'=>'Onaylı lisans'
                        ],200);
                    }else {
                        return response()->json([
                            'success'=>false,
                            'message'=>'Port güncellenemedi. Lütfen, satıcı firma ile irtibata geçiniz.'
                        ],201);
                    }

                }else {
                    return response()->json([
                        'success'=>false,
                        'message'=>'Bu lisans anahtarı başka bilgisayar tarafından kullanılmaktadır. Lütfen, satıcı firma ile irtibata geçiniz.'
                    ],201);
                }
            }
        }else {
            return response()->json([
                'success'=>false,
                'message'=>'Lisans anahtarı bulunamamaktadır.'
            ],201);
        }


    }

    public function register(Request $request){
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|string|confirmed'
        ]);

        $user = new User([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>md5($request->password)
        ]);
        $user = $user->save();

        if ($user) {
            $this->testMail($request->email);

            $saveUser = User::where('email',$request->email)->first();
            $role = new UserHasRole([
                'user_id'=>$saveUser->id,
                'role_id'=>2
            ]);
            $role = $role->save();
        }

        $credentials = ['email'=>$request->email,'password'=>$request->password];

        if(!Auth::attempt($credentials)){
            return response()->json([
                'message'=>'Giriş Yapılamadı Bilgileri Kontrol Ediniz'
            ],401);
        }
        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access');
        $token = $tokenResult->token;
        if($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        return response()->json([
            'success'=>true,
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email,
            'access_token'=>$tokenResult->accessToken,
            'token_type'=>'Bearer',
            'expires_at'=>Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ],201);
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string',
            'remember_me'=>'boolean'
        ]);
        $credentials = request(['email','password']);

        if(!Auth::attempt($credentials)){
            return response()->json([
                'message'=>'Bilgiler Hatalı Kontrol Ediniz'
            ],401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'success'=>true,
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email,
            'access_token'=>$tokenResult->accessToken,
            'token_type'=>'Bearer',
            'expires_at'=>Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ],201);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message'=>'Çıkış Yapıldı'
        ]);
    }

    public function user(Request $request){
        return response()->json($request->user());
    }

    public function authenticate(Request $request){
        $user = [];
        if(Auth::check()){
            $user = $request->user();
        }
        return response()->json([
            'user'=>$user,
            'isLoggedIn'=>Auth::check()
        ]);
    }

    public function testMail($email)
    {
        $user = User::where('email',$email)->first();
        $user = $user->toArray();

        $userEmail = $user["email"];
        $userName = $user["name"];

        $details = [
            'title' => 'Sisteme Kayıt',
            'body' =>'Sisteme kaydınız başarı ile sağlanmıştır.',
            'name' => $userName
        ];

        Mail::to($userEmail)->send(new SendMail($details));
        return true;
    }

    public function requestEncrypted($data)
    {
        $password = 'iJ4!Z86O2&92iMXrI';
        $method = 'aes-256-cbc';
        $password = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $encrypted = base64_encode(openssl_encrypt($data, $method, $password, OPENSSL_RAW_DATA, $iv));
        return $encrypted;
    }

    public function requestDecrypted($data)
    {
        $password = 'iJ4!Z86O2&92iMXrI';
        $method = 'aes-256-cbc';
        $password = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $decrypted = openssl_decrypt(base64_decode($data), $method, $password, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

}
