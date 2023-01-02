<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LogoCashPaymentRequest;
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
use App\Helper\Logo\logoCashPayment;
use App\Helper\Logo\logoCurrent;
class LogoCashPaymentController extends Controller
{

    public function cashPayment(Request $request){

        $license = License::where('licenseKey',$request->licenseKey)->first();
        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            dd("hata");
        }
    // tt
        $paymentDate = Carbon::parse($request->paymentDate)->format('d.m.Y');
        $paymentDateHour = Carbon::parse($request->paymentDate)->format('H');
        $paymentDateMinute = Carbon::parse($request->paymentDate)->format('i');
        $paymentDateMonth = Carbon::parse($request->paymentDate)->format('m');
        $paymentDateYear = Carbon::parse($request->paymentDate)->format('Y');

        $params = array();
        $params['IP'] = $ip;
        $params['PORT'] = $port;
        $params['DATE'] = $paymentDate;
        $params['DATE_HOUR'] = $paymentDateHour;
        $params['DATE_MINUTE'] = $paymentDateMinute;
        $params['DATE_MONTH'] = $paymentDateMonth;
        $params['DATE_YEAR'] = $paymentDateYear;
        $params['SD_CODE'] = $request->sd_code ? $request->sd_code : " ";
        $params['MASTER_TITLE'] = $request->description ? $request->description : " ";
        $params['DESCRIPTION'] = $request->description ? $request->description : " ";
        $params['DEPARTMENT'] = $request->department ? $request->department : " ";
        $params['TOTAL'] = $request->total ? $request->total : " ";
        $params['TYPE'] = $request->type ? $request->type : 11;
        $params['ARP_CODE'] = $request->currentId ? $request->currentId : " ";
        $params['COMPANY_ID'] = $request->companyId;
        
        $response = logoCashPayment::cashPaymentPostData($params);
        $responseMessage = $response->getBody()->getContents();

        try {
            $cashPaymentRequest = new LogoCashPaymentRequest;
            $cashPaymentRequest->request_data = json_encode($request->all(), JSON_UNESCAPED_UNICODE);
            $cashPaymentRequest->ip = $ip;
            $cashPaymentRequest->licenseKey = $request->licenseKey;
            $cashPaymentRequest->company_id = $request->companyId;
            $cashPaymentRequest->type = $request->type;
            $cashPaymentRequest->payment_date = $paymentDate;
            $cashPaymentRequest->current_id = $request->currentId;
            $cashPaymentRequest->price = $request->total;
            $cashPaymentRequest->description = $request->description;
            $cashPaymentRequest->status = $response->getStatusCode();
            $cashPaymentRequest->response_message = $responseMessage;
            $cashPaymentRequest->save();
        } catch (\Throwable $th) {
            \Log::info("Tahsilat Aktarımı kaydedilemedi ". $th);
        }


        if ($response->getStatusCode() == 200) {
            return response()->json([
                'success'=>true,
                'returnMessage'=>$responseMessage,
                'message'=>'Tahsilat aktarıldı.'
            ],200);
        }else {
            dd($response);
            return response()->json([
                'success'=>false,
                'returnMessage'=>$responseMessage,
                'message'=>'Tahsilat aktarılamadı!'
            ],201);
        }

    }

}
