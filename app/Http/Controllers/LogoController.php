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

        $request = $client->request('GET','http://213.238.176.215:1903', [
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

    public function itemPostData($params, $type = null)
    {
        $data = json_encode($params);

        $client = new Client(['verify' => false]);

        $INTERNAL_REFERENCE =  $params['INTERNAL_REFERENCE'];
        //$CARD_TYPE =  $params['CARD_TYPE'];
        //$CODE =  $params['CODE'];
        //$NAME =  $params['NAME'];
        //$PRODUCER_CODE =  $params['PRODUCER_CODE'];
        //$AUXIL_CODE =  $params['AUXIL_CODE'];
        //$USEF_PURCHASING =  $params['USEF_PURCHASING'];
        //$USEF_SALES =  $params['USEF_SALES'];
        //$USEF_MM =  $params['USEF_MM'];
        //$VAT =  $params['VAT'];
        //$AUTOINCSL =  $params['AUTOINCSL'];
        //$LOTS_DIVISIBLE =  $params['LOTS_DIVISIBLE'];
        //$UNITSET_CODE =  $params['UNITSET_CODE'];
        //$DIST_LOT_UNITS =  $params['DIST_LOT_UNITS'];
        //$COMB_LOT_UNITS =  $params['COMB_LOT_UNITS'];
        //$FACTORY_PARAM_INTERNAL_REFERENCE =  $params['FACTORY_PARAM_INTERNAL_REFERENCE'];
        //$UNIT_CODE =  $params['UNIT_CODE'];
        //$USEF_MTRLCLASS =  $params['USEF_MTRLCLASS'];
        //$USEF_PURCHCLAS =  $params['USEF_PURCHCLAS'];
        //$USEF_SALESCLAS =  $params['USEF_SALESCLAS'];
        //$CONV_FACT1 =  $params['CONV_FACT1'];
        //$CONV_FACT2 =  $params['CONV_FACT2'];
        //$UNIT_DATA_REFERENCE =  $params['UNIT_DATA_REFERENCE'];
        //$UNIT_INTERNAL_REFERENCE =  $params['UNIT_INTERNAL_REFERENCE'];
        //$GL_LINK_INTERNAL_REFERENCE =  $params['GL_LINK_INTERNAL_REFERENCE'];
        //$INFO_TYPE =  $params['INFO_TYPE'];
        //$GLACC_CODE =  $params['GLACC_CODE'];
        //$GL_LINK_DATA_REFERENCE =  $params['GL_LINK_DATA_REFERENCE'];
        //$EXT_ACC_FLAGS =  $params['EXT_ACC_FLAGS'];
        //$MULTI_ADD_TAX =  $params['MULTI_ADD_TAX'];
        //$PACKET =  $params['PACKET'];
        //$SELVAT =  $params['SELVAT'];
        //$RETURNVAT =  $params['RETURNVAT'];
        //$SELPRVAT =  $params['SELPRVAT'];
        //$RETURNPRVAT =  $params['RETURNPRVAT'];
        //$EXTCRD_FLAGS =  $params['EXTCRD_FLAGS'];
        //$UPDATECHILDS =  $params['UPDATECHILDS'];
        //$SALE_DEDUCTION_PART1 =  $params['SALE_DEDUCTION_PART1'];
        //$SALE_DEDUCTION_PART2 =  $params['SALE_DEDUCTION_PART2'];
        //$PURCH_DEDUCTION_PART1 =  $params['PURCH_DEDUCTION_PART1'];
        //$PURCH_DEDUCTION_PART2 =  $params['PURCH_DEDUCTION_PART2'];
        //$ITEM_SUBSTITUTE_INTERNAL_REFERENCE =  $params['ITEM_SUBSTITUTE_INTERNAL_REFERENCE'];

        $itemXmlRequest  = <<<XML
            <?xml version="1.0" encoding="ISO-8859-9"?>
                <ITEMS>
                <ITEM DBOP="INS">
                    <INTERNAL_REFERENCE>$INTERNAL_REFERENCE</INTERNAL_REFERENCE>
                    <CARD_TYPE>1</CARD_TYPE>
                    <CODE>TEST11</CODE>
                    <NAME>TEST11</NAME>
                    <PRODUCER_CODE>test11</PRODUCER_CODE>
                    <AUXIL_CODE>test11</AUXIL_CODE>
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
                            <INTERNAL_REFERENCE>10655</INTERNAL_REFERENCE>
                            <INFO_TYPE>1</INFO_TYPE>
                            <GLACC_CODE>153.01</GLACC_CODE>
                            <DATA_REFERENCE>10655</DATA_REFERENCE>
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
                    </ITEM>
                </ITEMS>
        XML;

        $request = $client->request('GET','http://213.238.176.215:1903', [
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'LogoStatus' => 'AR_APS',
                'RequestType' => 'Logo'
            ],
            'body' => $this->requestEncrypted($itemXmlRequest)
        ]);

        $response = $request->getBody()->getContents();
        dd($response);


        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response);
        $xml = simplexml_load_string($clean_xml);
        $data = json_decode($xml->Body->POST_RESERVATIONResponse->POST_RESERVATIONResult[0]);
        dd($data);
        return $data;
    }
}
