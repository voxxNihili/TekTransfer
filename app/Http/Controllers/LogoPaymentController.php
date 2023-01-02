<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LogoPaymentRequest;
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
use App\Helper\Logo\logoPayment;

class LogoPaymentController extends Controller
{

    public function payment(Request $request){
       
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
        $params['TYPE'] = $request->type ? $request->type : 0;
        $params['ARP_CODE'] = $request->currentId ? $request->currentId : " ";
        $params['BANKACC_CODE'] = $request->bankCode ? $request->bankCode : " ";
        $params['COMPANY_ID'] = $request->companyId;
        $params['DESCRIPTION'] = $request->description;
        $response = logoPayment::paymentPostData($params);

        try {
            $paymentRequest = new LogoPaymentRequest;
            $paymentRequest->request_data = json_encode($request->all(), JSON_UNESCAPED_UNICODE);
            $paymentRequest->ip = $ip;
            $paymentRequest->licenseKey = $request->licenseKey;
            $paymentRequest->company_id = $request->companyId;
            $paymentRequest->type = $request->type;
            $paymentRequest->payment_date = $paymentDate;
            $paymentRequest->current_id = $request->currentId;
            $paymentRequest->price = $request->total;
            $paymentRequest->status = $response->getStatusCode();
            $paymentRequest->response_message = $response->getBody()->getContents();
            $paymentRequest->save();
        } catch (\Throwable $th) {
            \Log::info("Tahsilat Aktarımı kaydedilemedi ". $th);
        }


        if ($response->getStatusCode() == 200) {
            return response()->json([
                'success'=>true,
                'returnMessage'=>$response->getBody()->getContents(),
                'message'=>'Tahsilat aktarıldı.'
            ],200);
        }else {
            dd($response->getBody()->getContents());
            return response()->json([
                'success'=>false,
                'returnMessage'=>$response->getBody()->getContents(),
                'message'=>'Tahsilat aktarılamadı!'
            ],201);
        }

    }

}
