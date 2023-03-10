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
use App\Helper\Logo\logoCurrent;
use App\Http\Controllers\api\queryController;

class LogoPaymentController extends Controller{

    public function payment(Request $request){
       
        $license = License::where('licenseKey',$request->licenseKey)->first();
        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            dd("hata");
        }
      
        $paymentDate = Carbon::parse($request->paymentDate)->format('d.m.Y');
        $paymentRequestPaymentDate = Carbon::parse($request->paymentDate)->format('Y-m-d H:i:s');

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

        $currentReq = new Request;
        $currentReq['licenseId'] = $license->id;
        $currentReq['companyId'] = $request->companyId;
        $currentReq['periodId'] = "01";
        $currentReq['query'] = ['**current**'=>$request->currentId];
        $currentReqCode = 'efatura';
        $currentReqQueryController = new queryController;
        $currentReqQuery = $currentReqQueryController->generateQuery($currentReq,$currentReqCode);
        $currentReqResponseData = json_decode($currentReqQuery->content());

        if ($currentReqResponseData->data[0]->STATUS == 0) {
            $currentParams = array();
            $currentParams['IP'] = $ip;
            $currentParams['PORT'] = $port;
            $currentParams['ACCOUNT_TYPE'] = 3; //$request->cPnrNo ? $request->cPnrNo :" ";
            $CODE = $request->currentId ? $request->currentId : " ";
            $currentParams['CODE'] = $CODE;
            $currentParams['TITLE'] = $request->companyTitle ? $request->companyTitle :" ";
            $currentParams['ADDRESS'] = " ";
            $currentParams['DISTRICT'] = " ";
            $currentParams['CITY'] = $request->city ? $request->city :" ";
            $currentParams['COUNTRY'] = $request->country ? $request->country :" ";
            $currentParams['TELEPHONE'] = $request->Telephone ? $request->Telephone :" ";
            $currentParams['NAME'] =$request->companyTitle ? $request->companyTitle :" ";
            $currentParams['SURNAME'] = " ";
            $currentParams['E_MAIL'] = $request->email ? $request->email :" ";
            $currentParams['TCKNO'] = " ";
            $currentParams['TAX_ID'] = " ";
            $currentParams['TAX_OFFICE'] = " ";
            $currentParams['COMPANY_ID'] = $request->companyId ? $request->companyId :" ";   
            $responseCurrent = logoCurrent::currentPostData($currentParams);
            if ($responseCurrent->getStatusCode() != 200) {
                $returnErrMsg = $responseCurrent->getBody()->getContents();
            }
        }

        $response = logoPayment::paymentPostData($params);
        $returnErrMsg = $response->getBody()->getContents();
        try {
            $paymentRequest = new LogoPaymentRequest;
            $paymentRequest->request_data = json_encode($request->all(), JSON_UNESCAPED_UNICODE);
            $paymentRequest->ip = $ip;
            $paymentRequest->licenseKey = $request->licenseKey;
            $paymentRequest->company_id = $request->companyId;
            $paymentRequest->type = $request->type;
            $paymentRequest->payment_date = $paymentRequestPaymentDate;
            $paymentRequest->current_id = $request->currentId;
            $paymentRequest->price = $request->total;
            $paymentRequest->status = $response->getStatusCode();
            $paymentRequest->response_message = $returnErrMsg;
            $paymentRequest->save();
        } catch (\Throwable $th) {
            \Log::info("Tahsilat Aktar??m?? kaydedilemedi ". $th);
        }

        if ($response->getStatusCode() == 200) {
            return response()->json([
                'success'=>true,
                'returnMessage'=>$returnErrMsg,
                'message'=>'Tahsilat aktar??ld??.' ],200);
        }else {
            return response()->json([
            'success'=>false,
            'returnMessage'=>$returnErrMsg,
            'message'=>'Tahsilat aktar??lamad??.'
            ],201); 
        }
    }
}
