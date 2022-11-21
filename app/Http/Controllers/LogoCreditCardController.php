<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Invoice;
use App\Models\License;
use App\Models\LogoSetting;
use App\Models\Order;
use App\Mail\SendMail;
use App\Models\Category;
use App\Models\UserHasRole;
use Mail;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use App\Helper\requestCrypt;
use App\Helper\Logo\logoCreditCard;

class LogoCreditCardController extends Controller
{

    public function creditCard(Request $request){

        $license = License::where('licenseKey',$request->licenseKey)->first();
        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            dd("hata");
        }

        $paymentDate = Carbon::parse($request->paymentDate)->format('d.m.Y');
        $params = array();
        $params['IP'] = $ip;
        $params['PORT'] = $port;
        $params['DATE'] = $paymentDate;
        $params['DEPARTMENT'] = $request->department ? $request->department : " ";
        $params['TOTAL'] = $request->total ? $request->total : " ";
        $params['ARP_CODE'] = $request->currentId ? $request->currentId : " ";
        $params['BANKACC_CODE'] = $request->bankCode ? $request->bankCode : " ";
        $params['COMPANY_ID'] = $request->companyId;
        $response = logoCreditCard::creditCardPostData($params);
        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
            return response()->json([
                'success'=>true,
                'returnMessage'=>$response->getBody()->getContents(),
                'message'=>'Kredi Kartı Tahsilatı aktarıldı.'
            ],200);
        }else {
            return response()->json([
                'success'=>false,
                'returnMessage'=>$response->getBody()->getContents(),
                'message'=>'Kredi Kartı Tahsilatı aktarılamadı!'
            ],201);
        }

    }

}
