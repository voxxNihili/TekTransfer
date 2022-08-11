<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\License;
use App\Models\Order;
use App\Mail\SendMail;
use Mail;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
class AuthController extends Controller
{
    public function hakan(){
        return response()->json([
            'success'=>true,
            'message'=>'SA.'
        ],200);
    }

    public function salesInvoice(Request $request){

        $invoice_date = Carbon::parse($request->invoiceDate)->format('d.m.Y');

        //$salesInvoiceRequest = new salesInvoiceRequest;

        $params = array();
        $params['INTERNAL_REFERENCE'] = " ";
        $params['TYPE'] = 9;
        $params['NUMBER'] = 99999;
        $params['DATE'] = $invoice_date;
        $params['TIME'] = "";
        $params['ARP_CODE'] = $request->cPnrNo ? $request->cPnrNo :" ";
        $params['GL_CODE'] = $request->cPnrNo ? $request->cPnrNo :" ";
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
                        <TYPE>4</TYPE>
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

        // $paymentsData = "";
        // foreach ($request->payments as $payment) {
        //     $dataPayments = '<PAYMENT>
        //                         <INTERNAL_REFERENCE>'.$payment['PAYMENT_INTERNAL_REFERENCE'].'</INTERNAL_REFERENCE>
        //                         <DATE>'.$payment['PAYMENT_DATE'].'</DATE>
        //                         <MODULENR>'.$payment['MODULENR'].'</MODULENR>
        //                         <TRCODE>'.$payment['TRCODE'].'</TRCODE>
        //                         <TOTAL>'.$payment['PAYMENT_TOTAL'].'</TOTAL>
        //                         <PROCDATE>'.$payment['PROCDATE'].'</PROCDATE>
        //                         <DATA_REFERENCE>'.$payment['PAYMENT_DATA_REFERENCE'].'</DATA_REFERENCE>
        //                         <DISCOUNT_DUEDATE>'.$payment['DISCOUNT_DUEDATE'].'</DISCOUNT_DUEDATE>
        //                         <DISCTRLIST>'.$payment['DISCTRLIST'].'</DISCTRLIST>
        //                         <DISCTRDELLIST>'.$payment['DISCTRDELLIST'].'</DISCTRDELLIST>
        //                     </PAYMENT>';
        //     $paymentsData .= $dataPayments;
        // }

        $params['PAYMENT_LIST'] = " ";

        $currentParams = array();
        //TaxNumber
        $currentParams['ACCOUNT_TYPE'] = 2; //$request->cPnrNo ? $request->cPnrNo :" ";
        $CODE = $request->cPnrNo ? "120.01.".$request->cPnrNo : " ";
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

        if ($responseCurrent["status"] == 200 || $responseCurrent["status"] == 201 ) {
            $response = collect($this->salesInvoicePostData($params));
            if ($response["status"] != 200) {
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
        }else {
            return response()->json([
                'success'=>true,
                'message'=>'Cari Kartı Oluşturulamadı'
            ],200);
        }
    }

    public function salesInvoicePostData($params, $type = null)
    {
        $data = json_encode($params);

        $client = new Client(['verify' => false]);

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
        //TRANSACTIONS
        // $TRANSACTION_INTERNAL_REFERENCE = $params['TRANSACTION_INTERNAL_REFERENCE'];
        // $TRANSACTION_TYPE = $params['TRANSACTION_TYPE'];
        // $MASTER_CODE = $params['MASTER_CODE'];
        // $GL_CODE2 = $params['GL_CODE2'];
        // $QUANTITY = $params['QUANTITY'];
        // $PRICE = $params['PRICE'];
        // $TOTAL = $params['TOTAL'];
        // $UNIT_CODE = $params['UNIT_CODE'];
        // $UNIT_CONV1 = $params['UNIT_CONV1'];
        // $UNIT_CONV2 = $params['UNIT_CONV2'];
        // $TRANSACTION_VAT_RATE = $params['TRANSACTION_VAT_RATE'];
        // $VAT_AMOUNT = $params['VAT_AMOUNT'];
        // $VAT_BASE = $params['VAT_BASE'];
        // $BILLED = $params['BILLED'];
        // $TRANSACTION_TOTAL_NET = $params['TRANSACTION_TOTAL_NET'];
        // $TRANSACTION_DATA_REFERENCE = $params['TRANSACTION_DATA_REFERENCE'];
        // $DIST_ORD_REFERENCE = $params['DIST_ORD_REFERENCE'];
        // $CAMPAIGN_INFO = $params['CAMPAIGN_INFO'];
        // $MULTI_ADD_TAX = $params['MULTI_ADD_TAX'];
        // $EDT_CURR = $params['EDT_CURR'];
        // $TRANSACTION_ORGLOGOID = $params['TRANSACTION_ORGLOGOID'];
        // $TRANSACTION_MONTH = $params['TRANSACTION_MONTH'];
        // $TRANSACTION_YEAR = $params['TRANSACTION_YEAR'];
        // $TRANSACTION_AFFECT_RISK = $params['TRANSACTION_AFFECT_RISK'];
        // $UNIT_GLOBAL_CODE = $params['UNIT_GLOBAL_CODE'];
        // $TRANSACTION_EDTCURR_GLOBAL_CODE = $params['TRANSACTION_EDTCURR_GLOBAL_CODE'];
        // $MASTER_DEF = $params['MASTER_DEF'];
        // $FOREIGN_TRADE_TYPE = $params['FOREIGN_TRADE_TYPE'];
        // $DISTRIBUTION_TYPE_WHS = $params['DISTRIBUTION_TYPE_WHS'];
        // $DISTRIBUTION_TYPE_FNO = $params['DISTRIBUTION_TYPE_FNO'];
        // $FUTURE_MONTH_BEGDATE = $params['FUTURE_MONTH_BEGDATE'];
        //TRANSACTION
        //PAYMENT_LIST
        // $PAYMENT_INTERNAL_REFERENCE = $params['PAYMENT_INTERNAL_REFERENCE'];
        // $PAYMENT_DATE = $params['PAYMENT_DATE'];
        // $MODULENR = $params['MODULENR'];
        // $TRCODE = $params['TRCODE'];
        // $PAYMENT_TOTAL = $params['PAYMENT_TOTAL'];
        // $PROCDATE = $params['PROCDATE'];
        // $PAYMENT_DATA_REFERENCE = $params['PAYMENT_DATA_REFERENCE'];
        // $DISCOUNT_DUEDATE = $params['DISCOUNT_DUEDATE'];
        // $DISCTRLIST = $params['DISCTRLIST'];
        // $DISCTRDELLIST = $params['DISCTRDELLIST'];
        //PAYMENT_LIST
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
                    <INTERNAL_REFERENCE>$INTERNAL_REFERENCE</INTERNAL_REFERENCE>
                    <TYPE>$TYPE</TYPE>
                    <NUMBER>$NUMBER</NUMBER>
                    <DATE>$DATE</DATE>
                    <TIME>$TIME</TIME>
                    <ARP_CODE>$ARP_CODE</ARP_CODE>
                    <GL_CODE>$GL_CODE</GL_CODE>
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

        $request = $client->request('GET','http://10.10.3.248:1903', [
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'LogoStatus' => 'SALES_INVOICES',
                'RequestType' => 'Logo'
            ],
            'body' => $xmlRequest
        ]);

        $response = $request->getBody()->getContents();
        dd($response);


        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response);
        $xml = simplexml_load_string($clean_xml);
        $data = json_decode($xml->Body->POST_RESERVATIONResponse->POST_RESERVATIONResult[0]);
        dd($data);
        return $data;
    }

    public function currentPostData($params, $type = null)
    {
        $data = json_encode($params);

        $client = new Client(['verify' => false]);

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
                    <ADDRESS1>$ADDRESS</ADDRESS1>
                    <DISTRICT>$DISTRICT</DISTRICT>
                    <TOWN_CODE></TOWN_CODE>
                    <TOWN></TOWN>
                    <CITY_CODE></CITY_CODE>
                    <CITY>$CITY</CITY>
                    <COUNTRY_CODE></COUNTRY_CODE>
                    <COUNTRY>$COUNTRY</COUNTRY>
                    <TELEPHONE1>$TELEPHONE</TELEPHONE1>
                    <TELEPHONE2></TELEPHONE2>
                    <CONTACT>$NAME</CONTACT>
                    <E_MAIL>$E_MAIL</E_MAIL>
                    <CORRESP_LANG></CORRESP_LANG>
                    <NOTES>
                        <NOTE>
                            <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
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
                    <PURCHBRWS></PURCHBRWS>
                    <SALESBRWS></SALESBRWS>
                    <IMPBRWS></IMPBRWS>
                    <EXPBRWS></EXPBRWS>
                    <FINBRWS></FINBRWS>
                    <ACTION_CREDHOLD_ORD></ACTION_CREDHOLD_ORD>
                    <ACTION_CREDHOLD_DESP></ACTION_CREDHOLD_DESP>
                    <PERSCOMPANY></PERSCOMPANY>
                    <TCKNO>$TCKNO</TCKNO>
                    <CONTACT2></CONTACT2>
                    <PROFILE_ID></PROFILE_ID>
                    <TITLE2></TITLE2>
                    <NAME>$NAME</NAME>
                    <SURNAME>$SURNAME</SURNAME>
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
                <ADDRESS1>$ADDRESS</ADDRESS1>
                <DISTRICT>$DISTRICT</DISTRICT>
                <TOWN_CODE></TOWN_CODE>
                <TOWN></TOWN>
                <CITY_CODE></CITY_CODE>
                <CITY>$CITY</CITY>
                <COUNTRY_CODE></COUNTRY_CODE>
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
                        <INTERNAL_REFERENCE></INTERNAL_REFERENCE>
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
                <PURCHBRWS></PURCHBRWS>
                <SALESBRWS></SALESBRWS>
                <IMPBRWS></IMPBRWS>
                <EXPBRWS></EXPBRWS>
                <FINBRWS></FINBRWS>
                <ACTION_CREDHOLD_ORD></ACTION_CREDHOLD_ORD>
                <ACTION_CREDHOLD_DESP></ACTION_CREDHOLD_DESP>
                <PERSCOMPANY></PERSCOMPANY>
                <TCKNO>$TCKNO</TCKNO>
                <CONTACT2></CONTACT2>
                <PROFILE_ID></PROFILE_ID>
                <TITLE2></TITLE2>
                <NAME>$NAME</NAME>
                <SURNAME>$SURNAME</SURNAME>
            </AR_AP>
        </AR_APS>
        XML;

        if ($TAX_ID == ' ') {
            $xmlRequest = $currentXmlRequest;
        }else {
            $xmlRequest = $companyXmlRequest;
        }

        $request = $client->request('GET','http://10.10.3.75:1903', [
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'LogoStatus' => 'AR_APS',
                'RequestType' => 'Logo'
            ],
            'body' => $this->requestEncrypted($xmlRequest)
        ]);

        $response = $request->getBody()->getContents();
        dd($response);


        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response);
        $xml = simplexml_load_string($clean_xml);
        $data = json_decode($xml->Body->POST_RESERVATIONResponse->POST_RESERVATIONResult[0]);
        dd($data);
        return $data;
    }

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
                    return response()->json([
                        'success'=> true,
                        'licenseDate'=> $this->requestEncrypted($endDate),
                        'dateTimer'=> $this->requestEncrypted($request->dateTimer),
                        'message'=>'Onaylı lisans'
                    ],200);
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
