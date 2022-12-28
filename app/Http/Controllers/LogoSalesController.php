<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Invoice;
use App\Models\License;
use App\Models\LogoSetting;
use App\Models\Order;
use App\Models\LogoCompany;
use App\Mail\SendMail;
use App\Models\Category;
use App\Models\UserHasRole;
use Mail;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use App\Helper\requestCrypt;
use App\Helper\Logo\logoCurrent;
use App\Helper\Logo\logoItem;
use App\Helper\Logo\logoSalesInvoice;
use App\Http\Controllers\api\queryController;

class LogoSalesController extends Controller
{

    public function salesInvoice(Request $request){
        $license = License::where('licenseKey',$request->licenseKey)->first();
        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            return response()->json([
                'success'=>false,
                'message'=>'Geçersiz Ürün Anahtarı!'
            ],201);
        }

        $invoice_date = Carbon::parse($request->invoiceDate)->format('d.m.Y');
        $params = array();
        $params['IP'] = $ip;
        $params['PORT'] = $port;
        $params['INTERNAL_REFERENCE'] = "190359";
        $params['TYPE'] = $request->type ? $request->type : 8;
        $params['NUMBER'] = $request->invoiceNumber ? $request->invoiceNumber :'~';
        $params['DATE'] = $invoice_date;
        $params['TIME'] = "";
        $params['LOCATION'] = $request->location ? $request->location : " ";
        $params['ARP_CODE'] = $request->cPnrNo ? $request->cPnrNo : " ";
        $params['GL_CODE'] = $request->cPnrNo ? $request->cPnrNo : " ";
        $params['POST_FLAGS'] = "";
        $params['VAT_RATE'] = " ";
        $params['TOTAL_DISCOUNTED'] = " ";
        $params['TOTAL_VAT'] = "";
        $params['TOTAL_GROSS'] = " ";
        $params['TOTAL_NET'] =" ";
        $params['NOTE'] = $request->note;
        $params['TC_NET'] = " ";
        $params['SINGLE_PAYMENT'] = 1;
        $params['COMPANY_ID'] = $request->companyId;
        $companyId = $request->companyId;
        $transactionsData = "";
        $itemsData = "";

        $currentParams = array();
        $currentParams['IP'] = $ip;
        $currentParams['PORT'] = $port;
        $currentParams['ACCOUNT_TYPE'] = 3; //$request->cPnrNo ? $request->cPnrNo :" ";
        $CODE = $request->cPnrNo ? $request->cPnrNo : " ";
        $currentParams['CODE'] = $CODE;
        $currentParams['TITLE'] = $request->companyTitle ? $request->companyTitle :" ";
        $currentParams['ADDRESS'] = $request->address ? $request->address :" ";
        $currentParams['DISTRICT'] = $request->district ? $request->district :" ";
        $currentParams['CITY'] = $request->city ? $request->city :" ";
        $currentParams['COUNTRY'] = $request->country ? $request->country :" ";
        $currentParams['TELEPHONE'] = $request->Telephone ? $request->Telephone :" ";
        $currentParams['NAME'] = $request->name ? $request->name :" ";
        $currentParams['SURNAME'] = $request->surname ? $request->surname :" ";
        $currentParams['E_MAIL'] = $request->email ? $request->email :" ";
        $currentParams['TCKNO'] = $request->personalIdentification ? $request->personalIdentification :" ";
        $currentParams['TAX_ID'] = $request->TaxNumber ? $request->TaxNumber :" ";
        $currentParams['TAX_OFFICE'] = $request->TaxAuthority ? $request->TaxAuthority :" ";
        $currentParams['COMPANY_ID'] = $request->companyId ? $request->companyId :" ";

        $responseCurrent = collect(logoCurrent::currentPostData($currentParams));

        foreach ($request->invoiceDetails as $invoiceDetail) {
            $productCode = @$invoiceDetail['productCode2'] ? @$invoiceDetail['productCode2'] : $invoiceDetail['productCode']; // yılbaşından sonra bu satır kaldırılacak, yerine direkt $invoiceDetail['productCode'] kullanılacak.
            $dataTransactions = '<TRANSACTION>
                        <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
                        <TYPE>'.$invoiceDetail['type'].'</TYPE>
                        <MASTER_CODE>'.$productCode.'</MASTER_CODE>
                        <GL_CODE2></GL_CODE2>
                        <QUANTITY>'.$invoiceDetail['quantity'].'</QUANTITY>
                        <PRICE>'.$invoiceDetail['price'].'</PRICE>
                        <TOTAL>'.$invoiceDetail['price'].'</TOTAL>
                        <DESCRIPTION>'.$invoiceDetail['description'].'</DESCRIPTION>
                        <UNIT_CODE>'.$invoiceDetail['unit'].'</UNIT_CODE>
                        <UNIT_CONV1></UNIT_CONV1>
                        <UNIT_CONV2></UNIT_CONV2>
                        <VAT_RATE>'.$invoiceDetail['taxRate'].'</VAT_RATE>
                        <VAT_AMOUNT></VAT_AMOUNT>
                        <VAT_BASE></VAT_BASE>
                        <BILLED></BILLED>
                        <TOTAL_NET></TOTAL_NET>
                        <DATA_REFERENCE>195976</DATA_REFERENCE>
                        <DIST_ORD_REFERENCE></DIST_ORD_REFERENCE>
                        <CAMPAIGN_INFOS>
                            <CAMPAIGN_INFO></CAMPAIGN_INFO>
                        </CAMPAIGN_INFOS>
                        <MULTI_ADD_TAX></MULTI_ADD_TAX>
                        <EDT_CURR></EDT_CURR>
                        <ORGLOGOID></ORGLOGOID>
                        <DEFNFLDSLIST>
                        </DEFNFLDSLIST>
                        <MONTH></MONTH>
                        <YEAR></YEAR>
                        <AFFECT_RISK></AFFECT_RISK>
                        <PREACCLINES></PREACCLINES>
                        <UNIT_GLOBAL_CODE></UNIT_GLOBAL_CODE>
                        <EDTCURR_GLOBAL_CODE></EDTCURR_GLOBAL_CODE>
                        <MASTER_DEF></MASTER_DEF>
                        <FOREIGN_TRADE_TYPE></FOREIGN_TRADE_TYPE>
                        <DISTRIBUTION_TYPE_WHS></DISTRIBUTION_TYPE_WHS>
                        <DISTRIBUTION_TYPE_FNO></DISTRIBUTION_TYPE_FNO>
                        <FUTURE_MONTH_BEGDATE></FUTURE_MONTH_BEGDATE>
                    </TRANSACTION>';
            $transactionsData .= $dataTransactions;
            $itemsData = $itemsData."('".$productCode."','".$invoiceDetail['productName']."','".$invoiceDetail['unit']."','".$invoiceDetail['type']."'),";
        }

        $itemsData = rtrim($itemsData,","); 
        $req = new Request;
        $req['licenseId'] = $license->id;
        $req['companyId'] = $companyId;
        $req['periodId'] = "01";
        $req['query'] = ['**value**'=>$itemsData];
        $reqCode = 'create_item';
        $queryController = new queryController;
        $reqQuery = $queryController->generateQuery($req,$reqCode);
        $responseData = json_decode($reqQuery->content());

        $params['TRANSACTIONS'] = $transactionsData;
        $params['PAYMENT_LIST'] = " ";

        $response = logoSalesInvoice::salesInvoicePostData($params);
        $responseMessage = $response->getBody()->getContents();

        $logoCompany = new LogoCompany;
        $logoCompany = $logoCompany->where('company_id',$companyId)->first();

        try {
            $invoice = new Invoice;
            $invoice->request_data = json_encode($request->all(), JSON_UNESCAPED_UNICODE);
            $invoice->ip = $ip;
            $invoice->type = $request->type;
            $invoice->invoice_date = Carbon::parse($request->invoiceDate)->format('Y-m-d H:i:s');
            $invoice->current = $request->cPnrNo;
            $invoice->customer_name = $request->fullname;
            $invoice->company_id = $companyId;
            $invoice->company_name = $logoCompany ? $logoCompany->name : null;
            $invoice->status = $response->getStatusCode();
            $invoice->response_message = $responseMessage;
            $invoice->save();
        } catch (\Throwable $th) {
            dd($th);
            \Log::info("Fatura kaydedilemedi ". $th);
        }

        if ($response->getStatusCode() != 200) {
            return response()->json([
                'success'=>false,
                'responseMessage'=>$responseMessage,
                'message'=>'Satış faturası aktarılamadı!'
            ],201);
        }else {
            \Log::channel('logoSalesInvoice')->info("Satış Faturası Aktarıldı");

            return response()->json([
                'success'=>true,
                'responseMessage'=>$responseMessage,
                'message'=>'Satış Faturası aktarıldı.'
            ],200);
        }

    }

}
