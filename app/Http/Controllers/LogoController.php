<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\License;
use App\Models\Order;
use App\Mail\SendMail;
use App\Models\Category;
use App\Models\UserHasRole;
use Mail;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
class LogoController extends Controller
{
    public function hakan(){

        $users = User::with('UserHasRole.role')->get();

        return response()->json([
            'success'=>true,
            'users'=>$users,
            'message'=>'SA.'
        ],200);
    }

    public function salesInvoice(Request $request){

        $license = License::where('licenseKey',$request->cVeritabaniAdi)->first();
        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            dd("hata");
        }

        $invoice_date = Carbon::parse($request->invoiceDate)->format('d.m.Y');

        //$salesInvoiceRequest = new salesInvoiceRequest;

        $params = array();
        $params['IP'] = $ip;
        $params['PORT'] = $port;
        $params['INTERNAL_REFERENCE'] = "190355";
        $params['TYPE'] = 8;
        $params['NUMBER'] = '~';
        $params['DATE'] = $invoice_date;
        $params['TIME'] = "";
        $params['ARP_CODE'] = $request->cPnrNo ? $request->cPnrNo : " ";
        $params['GL_CODE'] = $request->cPnrNo ? $request->cPnrNo : " ";
        $params['POST_FLAGS'] = "";
        $params['VAT_RATE'] = " ";
        $params['TOTAL_DISCOUNTED'] = " ";
        $params['TOTAL_VAT'] = "";
        $params['TOTAL_GROSS'] = " ";
        $params['TOTAL_NET'] =" ";
        $params['TC_NET'] = " ";
        $params['SINGLE_PAYMENT'] = 1;
        $transactionsData = "";

        foreach ($request->invoiceDetails as $invoiceDetail) {
            $dataTransactions = '<TRANSACTION>
                        <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
                        <TYPE>0</TYPE>
                        <MASTER_CODE>'.$invoiceDetail['productCode'].'</MASTER_CODE>
                        <GL_CODE2></GL_CODE2>
                        <QUANTITY>'.$invoiceDetail['quantity'].'</QUANTITY>
                        <PRICE>'.$invoiceDetail['price'].'</PRICE>
                        <TOTAL>'.$invoiceDetail['price'].'</TOTAL>
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
        }

        $params['TRANSACTIONS'] = $transactionsData;

        $params['PAYMENT_LIST'] = " ";

        $currentParams = array();
        //TaxNumber

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

        $responseCurrent = collect($this->currentPostData($currentParams));

        foreach ($request->invoiceDetails as $invoiceDetail) {
            $itemdata = '<ITEM DBOP="INS">
                <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
                <CARD_TYPE>1</CARD_TYPE>
                <CODE>'.$invoiceDetail['productCode'].'</CODE>
                <NAME>'.$invoiceDetail['productCode'].'</NAME>
                <PRODUCER_CODE>'.$invoiceDetail['productCode'].'</PRODUCER_CODE>
                <AUXIL_CODE>TM</AUXIL_CODE>
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

            $responseItem = collect($this->itemPostData($itemdata, $ip, $port));
        }


        //if ($responseCurrent["status"] == 200 || $responseCurrent["status"] == 201 ) {
            $response = $this->salesInvoicePostData($params);
            if ($response->getStatusCode() != 200) {
                return response()->json([
                    'success'=>false,
                    'message'=>'Satış faturası aktarılamadı!'
                ],201);
            }else {
                return response()->json([
                    'success'=>true,
                    'message'=>'Satış Faturası aktarıldı.'
                ],200);
            }
        // }else {
        //     return response()->json([
        //         'success'=>true,
        //         'message'=>'Cari Kartı Oluşturulamadı'
        //     ],200);
        // }
    }

    public function salesInvoicePostData($params, $type = null)
    {
        $data = json_encode($params);

        $client = new Client(['verify' => false]);

        $ip = $params['IP'];
        $port = $params['PORT'];
        $INTERNAL_REFERENCE =  $params['INTERNAL_REFERENCE'];
        $TYPE = $params['TYPE'];
        $NUMBER = $params['NUMBER'];
        $DATE = $params['DATE'];
        $TIME = $params['TIME'];
        $ARP_CODE = $params['ARP_CODE'];
        $GL_CODE = $params['GL_CODE'];
        $POST_FLAGS = $params['POST_FLAGS'];
        $VAT_RATE = $params['VAT_RATE'];
        $TOTAL_DISCOUNTED = $params['TOTAL_DISCOUNTED'];
        $TOTAL_VAT = $params['TOTAL_VAT'];
        $TOTAL_GROSS = $params['TOTAL_GROSS'];
        $TOTAL_NET = $params['TOTAL_NET'];
        $TC_NET = $params['TC_NET'];
        $SINGLE_PAYMENT = $params['SINGLE_PAYMENT'];
        $TRANSACTIONS = $params['TRANSACTIONS'];
        $PAYMENT_LIST = $params['PAYMENT_LIST'];
        $DEDUCTIONPART1 = '';
        $DEDUCTIONPART2 = '';
        $DATA_LINK_REFERENCE = '';
        $LOGICALREF = '';
        $AFFECT_RISK = '';
        $PREACCLINES = '';
        $DOC_DATE = '';
        $EINVOICE = '';
        $EDURATION_TYPE = '';
        $EDTCURR_GLOBAL_CODE = '';
        $TOTAL_NET_STR = '';
        $TOTAL_SERVICES = '';
        $EXIMVAT = '';
        $EARCHIVEDETR_LOGICALREF = '';
        $EARCHIVEDETR_INVOICEREF ='';
        $EARCHIVEDETR_SENDMOD = '';
        $EARCHIVEDETR_INTPAYMENTTYPE = '';
        $EARCHIVEDETR_INTPAYMENTDATE = '';
        $OKCINFO_INTERNAL_REFERENCE = '';

        $xmlRequest  = <<<XML
        <?xml version="1.0" encoding="ISO-8859-9"?>
            <SALES_INVOICES>
                <INVOICE DBOP="INS" >
                    <TYPE>$TYPE</TYPE>
                    <NUMBER>~</NUMBER>
                    <DATE>$DATE</DATE>
                    <TIME>$TIME</TIME>
                    <ARP_CODE>$ARP_CODE</ARP_CODE>
                    <POST_FLAGS>$POST_FLAGS</POST_FLAGS>
                    <VAT_RATE>$VAT_RATE</VAT_RATE>
                    <TOTAL_DISCOUNTED>$TOTAL_DISCOUNTED</TOTAL_DISCOUNTED>
                    <TOTAL_VAT>$TOTAL_VAT</TOTAL_VAT>
                    <TOTAL_GROSS>$TOTAL_GROSS</TOTAL_GROSS>
                    <TOTAL_NET>$TOTAL_NET</TOTAL_NET>
                    <TC_NET>$TC_NET</TC_NET>
                    <SINGLE_PAYMENT>$SINGLE_PAYMENT</SINGLE_PAYMENT>
                    <DISPATCHES>
                    </DISPATCHES>
                    <TRANSACTIONS>
                        $TRANSACTIONS
                    </TRANSACTIONS>
                    <PAYMENT_LIST>
                        $PAYMENT_LIST
                    </PAYMENT_LIST>
                    <ORGLOGOID></ORGLOGOID>
                    <DEFNFLDSLIST></DEFNFLDSLIST>
                    <DEDUCTIONPART1>$DEDUCTIONPART1</DEDUCTIONPART1>
                    <DEDUCTIONPART2>$DEDUCTIONPART2</DEDUCTIONPART2>
                    <DATA_LINK_REFERENCE>$DATA_LINK_REFERENCE</DATA_LINK_REFERENCE>
                    <INTEL_LIST>
                        <INTEL>
                            <LOGICALREF>$LOGICALREF</LOGICALREF>
                        </INTEL>
                    </INTEL_LIST>
                    <AFFECT_RISK>$AFFECT_RISK</AFFECT_RISK>
                    <PREACCLINES>$PREACCLINES</PREACCLINES>
                    <DOC_DATE>$DOC_DATE</DOC_DATE>
                    <EINVOICE>$EINVOICE</EINVOICE>
                    <EDURATION_TYPE>$EDURATION_TYPE</EDURATION_TYPE>
                    <EDTCURR_GLOBAL_CODE>$EDTCURR_GLOBAL_CODE</EDTCURR_GLOBAL_CODE>
                    <TOTAL_NET_STR>$TOTAL_NET_STR</TOTAL_NET_STR>
                    <TOTAL_SERVICES>$TOTAL_SERVICES</TOTAL_SERVICES>
                    <EXIMVAT>$EXIMVAT</EXIMVAT>
                    <EARCHIVEDETR_LOGICALREF>$EARCHIVEDETR_LOGICALREF</EARCHIVEDETR_LOGICALREF>
                    <EARCHIVEDETR_INVOICEREF>$EARCHIVEDETR_INVOICEREF</EARCHIVEDETR_INVOICEREF>
                    <EARCHIVEDETR_SENDMOD>$EARCHIVEDETR_SENDMOD</EARCHIVEDETR_SENDMOD>
                    <EARCHIVEDETR_INTPAYMENTTYPE>$EARCHIVEDETR_INTPAYMENTTYPE</EARCHIVEDETR_INTPAYMENTTYPE>
                    <EARCHIVEDETR_INTPAYMENTDATE>$EARCHIVEDETR_INTPAYMENTDATE</EARCHIVEDETR_INTPAYMENTDATE>
                    <OKCINFO_LIST>
                        <OKCINFO>
                            <INTERNAL_REFERENCE>$OKCINFO_INTERNAL_REFERENCE</INTERNAL_REFERENCE>
                        </OKCINFO>
                    </OKCINFO_LIST>
                    <LABEL_LIST>
                    </LABEL_LIST>
                </INVOICE>
            </SALES_INVOICES>
        XML;
        $request = $client->request('GET','http://'.$ip.':'.$port, [
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'LogoStatus' => 'SALES_INVOICES',
                'RequestType' => 'Logo'
            ],
            'body' => $this->requestEncrypted($xmlRequest)
        ]);

        return $request;
        $response = $request->getBody();
        $status = $request->getStatusCode();


        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response);
        dd($clean_xml);
        $xml = simplexml_load_string($clean_xml);
        //$data = json_decode($xml->Body->POST_RESERVATIONResponse->POST_RESERVATIONResult[0]);
        dd($data);
        return $data;
    }

    public function currentPostData($params, $type = null)
    {
        $data = json_encode($params);

        $client = new Client(['verify' => false]);
        $ip =  $params['IP'];
        $port = $params['PORT'];
        $ACCOUNT_TYPE =  $params['ACCOUNT_TYPE'];
        $CODE = $params['CODE'];
        $TITLE = $params['TITLE'];
        $ADDRESS = $params['ADDRESS'];
        $DISTRICT = $params['DISTRICT'];
        $CITY = $params['CITY'];
        $COUNTRY = $params['COUNTRY'];
        $TELEPHONE = $params['TELEPHONE'];
        $NAME = $params['NAME'];
        $SURNAME = $params['SURNAME'];
        $E_MAIL = $params['E_MAIL'];
        $TCKNO = $params['TCKNO'];
        $TAX_ID = $params['TAX_ID'];
        $TAX_OFFICE = $params['TAX_OFFICE'];

        $currentXmlRequest  = <<<XML
            <?xml version="1.0" encoding="ISO-8859-9"?>
            <AR_APS>
                <AR_AP DBOP="INS">
                    <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
                    <ACCOUNT_TYPE>$ACCOUNT_TYPE</ACCOUNT_TYPE>
                    <CODE>$CODE</CODE>
                    <TITLE>$TITLE</TITLE>
                    <CORRESP_LANG>1</CORRESP_LANG>
                    <ADDRESS1>$ADDRESS</ADDRESS1>
                    <DISTRICT>$DISTRICT</DISTRICT>
                    <TOWN_CODE></TOWN_CODE>
                    <TOWN>$DISTRICT</TOWN>
                    <CITY_CODE></CITY_CODE>
                    <CITY>$CITY</CITY>
                    <COUNTRY_CODE>TR</COUNTRY_CODE>
                    <COUNTRY>$COUNTRY</COUNTRY>
                    <TELEPHONE1>$TELEPHONE</TELEPHONE1>
                    <TELEPHONE2></TELEPHONE2>
                    <CONTACT>$NAME</CONTACT>
                    <E_MAIL>$E_MAIL</E_MAIL>
                    <CORRESP_LANG></CORRESP_LANG>
                    <NOTES>
                        <NOTE>
                            <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                        </NOTE>
                    </NOTES>
                    <CREDIT_TYPE></CREDIT_TYPE>
                    <RISKFACT_CHQ></RISKFACT_CHQ>
                    <RISKFACT_PROMNT></RISKFACT_PROMNT>
                    <AUTO_PAID_BANK></AUTO_PAID_BANK>
                    <CL_ORD_FREQ>1</CL_ORD_FREQ>
                    <LOGOID></LOGOID>
                    <CELL_PHONE></CELL_PHONE>
                    <INVOICE_PRNT_CNT>1</INVOICE_PRNT_CNT>
                    <GENIUSFLDSLIST> </GENIUSFLDSLIST>
                    <DEFNFLDSLIST> </DEFNFLDSLIST>
                    <ORGLOGOID/>
                    <PURCHBRWS>1</PURCHBRWS>
                    <SALESBRWS>1</SALESBRWS>
                    <IMPBRWS>1</IMPBRWS>
                    <EXPBRWS>1</EXPBRWS>
                    <FINBRWS>1</FINBRWS>
                    <COLLATRLRISK_TYPE>1</COLLATRLRISK_TYPE>
                    <RISK_TYPE1>1</RISK_TYPE1>
                    <RISK_TYPE2>1</RISK_TYPE2>
                    <RISK_TYPE3>1</RISK_TYPE3>
                    <ACTION_CREDHOLD_ORD></ACTION_CREDHOLD_ORD>
                    <ACTION_CREDHOLD_DESP></ACTION_CREDHOLD_DESP>
                    <PERSCOMPANY>1</PERSCOMPANY>
                    <TCKNO>$TCKNO</TCKNO>
                    <CONTACT2></CONTACT2>
                    <PROFILE_ID></PROFILE_ID>
                    <TITLE2></TITLE2>
                    <NAME>$NAME</NAME>
                    <SURNAME>$SURNAME</SURNAME>
                    <PROFILEID_DESP>1</PROFILEID_DESP>
                    <DISP_PRINT_CNT>1</DISP_PRINT_CNT>
                    <ORD_PRINT_CNT>1</ORD_PRINT_CNT>
                </AR_AP>
            </AR_APS>
        XML;

        $companyXmlRequest  = <<<XML
        <?xml version="1.0" encoding="ISO-8859-9"?>
        <AR_APS>
            <AR_AP DBOP="INS">
                <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
                <ACCOUNT_TYPE>$ACCOUNT_TYPE</ACCOUNT_TYPE>
                <CODE>$CODE</CODE>
                <TITLE>$TITLE</TITLE>
                <CORRESP_LANG>1</CORRESP_LANG>
                <ADDRESS1>$ADDRESS</ADDRESS1>
                <DISTRICT>$DISTRICT</DISTRICT>
                <TOWN_CODE></TOWN_CODE>
                <TOWN></TOWN>
                <CITY_CODE></CITY_CODE>
                <CITY>$CITY</CITY>
                <COUNTRY_CODE>TR</COUNTRY_CODE>
                <COUNTRY>$COUNTRY</COUNTRY>
                <TELEPHONE1>$TELEPHONE</TELEPHONE1>
                <TELEPHONE2></TELEPHONE2>
                <TAX_ID>$TAX_ID</TAX_ID>
                <TAX_OFFICE>$TAX_OFFICE</TAX_OFFICE>
                <CONTACT>$NAME</CONTACT>
                <E_MAIL>$E_MAIL</E_MAIL>
                <CORRESP_LANG></CORRESP_LANG>
                <NOTES>
                    <NOTE>
                        <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                    </NOTE>
                </NOTES>
                <CREDIT_TYPE></CREDIT_TYPE>
                <RISKFACT_CHQ></RISKFACT_CHQ>
                <RISKFACT_PROMNT></RISKFACT_PROMNT>
                <AUTO_PAID_BANK></AUTO_PAID_BANK>
                <CL_ORD_FREQ></CL_ORD_FREQ>
                <LOGOID></LOGOID>
                <CELL_PHONE></CELL_PHONE>
                <INVOICE_PRNT_CNT></INVOICE_PRNT_CNT>
                <GENIUSFLDSLIST> </GENIUSFLDSLIST>
                <DEFNFLDSLIST> </DEFNFLDSLIST>
                <ORGLOGOID/>
                <PURCHBRWS>1</PURCHBRWS>
                <SALESBRWS>1</SALESBRWS>
                <IMPBRWS>1</IMPBRWS>
                <EXPBRWS>1</EXPBRWS>
                <FINBRWS>1</FINBRWS>
                <COLLATRLRISK_TYPE>1</COLLATRLRISK_TYPE>
                <RISK_TYPE1>1</RISK_TYPE1>
                <RISK_TYPE2>1</RISK_TYPE2>
                <RISK_TYPE3>1</RISK_TYPE3>
                <ACTION_CREDHOLD_ORD></ACTION_CREDHOLD_ORD>
                <ACTION_CREDHOLD_DESP></ACTION_CREDHOLD_DESP>
                <PERSCOMPANY></PERSCOMPANY>
                <TCKNO>$TCKNO</TCKNO>
                <CONTACT2></CONTACT2>
                <PROFILE_ID></PROFILE_ID>
                <TITLE2></TITLE2>
                <NAME>$NAME</NAME>
                <SURNAME>$SURNAME</SURNAME>
                <DISP_PRINT_CNT>1</DISP_PRINT_CNT>
                <ORD_PRINT_CNT>1</ORD_PRINT_CNT>
            </AR_AP>
        </AR_APS>
        XML;

        if ($TAX_ID == ' ') {
            $xmlRequest = $currentXmlRequest;
        }else {
            $xmlRequest = $companyXmlRequest;
        }

        $request = $client->request('GET','http://'.$ip.':'.$port, [
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'LogoStatus' => 'AR_APS',
                'RequestType' => 'Logo'
            ],
            'body' => $this->requestEncrypted($xmlRequest)
        ]);
        //dd($request->getBody()->getContents());
        $response = $request->getBody()->getContents();
        //return $response;
    //     dd($response);


    //     $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response);
    //     $xml = simplexml_load_string($clean_xml);
    //     $data = json_decode($xml->Body->POST_RESERVATIONResponse->POST_RESERVATIONResult[0]);
    //     dd($data);
    //     return $data;
    }

    public function itemPostData($params, $ip, $port)
    {
        $itemData = $params;
        $client = new Client(['verify' => false]);
        $itemXmlRequest  = <<<XML
            <?xml version="1.0" encoding="ISO-8859-9"?>
                <ITEMS>
                    $itemData
                </ITEMS>
            XML;

        $request = $client->request('GET','http://'.$ip.':'.$port, [
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'LogoStatus' => 'ITEMS',
                'RequestType' => 'Logo'
            ],
            'body' => $this->requestEncrypted($itemXmlRequest)
        ]);

        $response = $request->getBody()->getContents();
        // dd($response);


        // $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response);
        // $xml = simplexml_load_string($clean_xml);
        // $data = json_decode($xml->Body->POST_RESERVATIONResponse->POST_RESERVATIONResult[0]);
        // dd($data);
        // return $data;
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
