<?php 
namespace App\Helper\Logo;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Helper\requestCrypt;

class logoCurrent
{
    static function currentPostData($params)
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
            'body' => requestCrypt::requestEncrypted($xmlRequest)
        ]);
        $response = $request->getBody()->getContents();
    }
}