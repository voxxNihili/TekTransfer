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
use App\Helper\Logo\logoCurrent;
use App\Helper\Logo\logoItem;
use App\Helper\Logo\logoPurchaseInvoice;

class LogoPurchaseController extends Controller
{

    public function salesInvoice(Request $request){

        $license = License::where('licenseKey',$request->licenseKey)->first();
        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            dd("hata");
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
            $dataTransactions = '<TRANSACTION>
                        <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
                        <TYPE>'.$invoiceDetail['type'].'</TYPE>
                        <MASTER_CODE>'.$invoiceDetail['productCode'].'</MASTER_CODE>
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
            $dataItems = '<ITEM DBOP="INS">
                    <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
                    <CARD_TYPE>1</CARD_TYPE>
                    <CODE>'.$invoiceDetail['productCode'].'</CODE>
                    <NAME>'.$invoiceDetail['productCode'].'</NAME>
                    <PRODUCER_CODE>'.$invoiceDetail['productCode'].'</PRODUCER_CODE>
                    <USEF_PURCHASING>1</USEF_PURCHASING>
                    <USEF_SALES>1</USEF_SALES>
                    <USEF_MM>1</USEF_MM>
                    <VAT>18</VAT>
                    <AUTOINCSL>1</AUTOINCSL>
                    <LOTS_DIVISIBLE>1</LOTS_DIVISIBLE>
                    <UNITSET_CODE>05</UNITSET_CODE>
                    <DIST_LOT_UNITS>1</DIST_LOT_UNITS>
                    <COMB_LOT_UNITS>1</COMB_LOT_UNITS>
                    <FACTORY_PARAMS>
                        <FACTORY_PARAM>
                            <INTERNAL_REFERENCE>481</INTERNAL_REFERENCE>
                        </FACTORY_PARAM>
                    </FACTORY_PARAMS>
                    <WH_PARAMS> </WH_PARAMS>
                    <CHARACTERISTICS> </CHARACTERISTICS>
                    <DOMINANT_CLASSES> </DOMINANT_CLASSES>
                    <UNITS>
                    <UNIT>
                        <UNIT_CODE>ADET</UNIT_CODE>
                        <USEF_MTRLCLASS>1</USEF_MTRLCLASS>
                        <USEF_PURCHCLAS>1</USEF_PURCHCLAS>
                        <USEF_SALESCLAS>1</USEF_SALESCLAS>
                        <CONV_FACT1>1</CONV_FACT1>
                        <CONV_FACT2>1</CONV_FACT2>
                        <DATA_REFERENCE>76911</DATA_REFERENCE>
                        <INTERNAL_REFERENCE>76911</INTERNAL_REFERENCE>
                        <BARCODE_LIST> </BARCODE_LIST>
                    </UNIT>
                    </UNITS>
                    <GL_LINKS>
                        <GL_LINK>
                            <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                            <INFO_TYPE>1</INFO_TYPE>
                        </GL_LINK>
                    </GL_LINKS>
                    <SUPPLIERS>
                    <SUPPLIER>
                        <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                        <PACKET_CODE/>
                        <UNIT_CODE/>
                        <UNITSET_CODE/>
                    </SUPPLIER>
                    </SUPPLIERS>
                    <EXT_ACC_FLAGS>3</EXT_ACC_FLAGS>
                    <MULTI_ADD_TAX>0</MULTI_ADD_TAX>
                    <PACKET>0</PACKET>
                    <SELVAT>18</SELVAT>
                    <RETURNVAT>18</RETURNVAT>
                    <SELPRVAT>18</SELPRVAT>
                    <RETURNPRVAT>18</RETURNPRVAT>
                    <EXTCRD_FLAGS>63</EXTCRD_FLAGS>
                    <GENIUSFLDSLIST> </GENIUSFLDSLIST>
                    <DEFNFLDSLIST> </DEFNFLDSLIST>
                    <ORGLOGOID/>
                    <UPDATECHILDS>1</UPDATECHILDS>
                    <SALE_DEDUCTION_PART1>2</SALE_DEDUCTION_PART1>
                    <SALE_DEDUCTION_PART2>3</SALE_DEDUCTION_PART2>
                    <PURCH_DEDUCTION_PART1>2</PURCH_DEDUCTION_PART1>
                    <PURCH_DEDUCTION_PART2>3</PURCH_DEDUCTION_PART2>
                    <ALTERNATIVES>
                    <ITEM_SUBSTITUTE>
                        <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                        <SUBS_CODE/>
                        <MAIN_CODE/>
                    </ITEM_SUBSTITUTE>
                    </ALTERNATIVES>
                    <LABEL_LIST> </LABEL_LIST>
                </ITEM>';
                $itemsData .= $dataItems;
        }

        $responseItem = collect(logoItem::itemPostData($itemsData, $ip, $port, $companyId));
      
        $params['TRANSACTIONS'] = $transactionsData;
        $params['PAYMENT_LIST'] = " ";


        $response = logoPurchaseInvoice::purchaseInvoicePostData($params);
        $responseMessage = $response->getBody()->getContents();

        try {
            $invoice = new Invoice;
            $invoice->request_data = json_encode($request->all(), JSON_UNESCAPED_UNICODE);
            $invoice->ip = $ip;
            $invoice->type = $request->type;
            $invoice->invoice_date = $invoice_date;
            $invoice->current = $request->cPnrNo;
            $invoice->customer_name = $request->fullname;
            $invoice->company_id = $companyId;
            $invoice->status = $response->getStatusCode();
            $invoice->response_message = $responseMessage;
            $invoice->save();
        } catch (\Throwable $th) {
            \Log::info("Fatura kaydedilemedi ". $th);
        }

        if ($response->getStatusCode() != 200) {
            return response()->json([
                'success'=>false,
                'responseMessage'=>$responseMessage,
                'message'=>'Alım faturası aktarılamadı!'
            ],201);
        }else {
            \Log::channel('logoPurchaseInvoice')->info("Alım Faturası Aktarıldı");

            return response()->json([
                'success'=>true,
                'responseMessage'=>$responseMessage,
                'message'=>'Alım Faturası aktarıldı.'
            ],200);
        }

    }

}
