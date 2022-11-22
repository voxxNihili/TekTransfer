<?php 
namespace App\Helper\Logo;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Helper\requestCrypt;

class logoCashPayment
{
    static function cashPaymentPostData($params)
    {
        try {
            $data = json_encode($params);
            $client = new Client(['verify' => false]);
            $ip =  $params['IP'];
            $port = $params['PORT'];
            $DATE =  $params['DATE'];
            $DEPARTMENT = $params['DEPARTMENT'];
            $TYPE = $params['TYPE'];
            $TOTAL = $params['TOTAL'];
            $ARP_CODE = $params['ARP_CODE'];
            $COMPANY_ID = $params['COMPANY_ID'];
            $DATE_HOUR =  $params['DATE_HOUR'];
            $DATE_MINUTE =  $params['DATE_MINUTE'];
            $DATE_MONTH =  $params['DATE_MONTH'];
            $DATE_YEAR =  $params['DATE_YEAR'];
            $SD_CODE =  $params['SD_CODE'];
            $MASTER_TITLE =  $params['MASTER_TITLE'];
            $DESCRIPTION =  $params['DESCRIPTION'];

            $cashPaymentXmlRequest  = <<<XML
                <?xml version="1.0" encoding="ISO-8859-9"?>
                    <SD_TRANSACTIONS>
                        <SD_TRANSACTION DBOP="INS" >
                            <INTERNAL_REFERENCE>1</INTERNAL_REFERENCE>
                            <TYPE>$TYPE</TYPE>
                            <SD_CODE>$SD_CODE</SD_CODE>
                            <SD_CODE_CROSS></SD_CODE_CROSS>
                            <SD_NUMBER_CROSS></SD_NUMBER_CROSS>
                            <CROSS_DATA_REFERENCE>15</CROSS_DATA_REFERENCE>
                            <DATE>$DATE</DATE>
                            <HOUR>$DATE_HOUR</HOUR>
                            <MINUTE>$DATE_MINUTE</MINUTE>
                            <DEPARTMENT>$DEPARTMENT</DEPARTMENT>
                            <NUMBER>~</NUMBER>
                            <MASTER_TITLE>$MASTER_TITLE</MASTER_TITLE>
                            <DESCRIPTION>$DESCRIPTION</DESCRIPTION>
                            <AMOUNT>$TOTAL</AMOUNT>
                            <TC_AMOUNT>$TOTAL</TC_AMOUNT>
                            <DATA_REFERENCE>1</DATA_REFERENCE>
                            <ATTACHMENT_ARP>
                            <TRANSACTION>
                                <INTERNAL_REFERENCE>15</INTERNAL_REFERENCE>
                                <ARP_CODE>$ARP_CODE</ARP_CODE>
                                <TRANNO>~</TRANNO>
                                <DESCRIPTION>$DESCRIPTION</DESCRIPTION>
                                <CREDIT>$TOTAL</CREDIT>
                                <TC_AMOUNT>$TOTAL</TC_AMOUNT>
                                <PAYMENT_LIST>
                                <PAYMENT>
                                    <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                                    <DATE>$DATE</DATE>
                                    <MODULENR>10</MODULENR>
                                    <SIGN>1</SIGN>
                                    <TRCODE>1</TRCODE>
                                    <TOTAL>$TOTAL</TOTAL>
                                    <PROCDATE>$DATE</PROCDATE>
                                    <DATA_REFERENCE>0</DATA_REFERENCE>
                                    <DISCOUNT_DUEDATE>$DATE</DISCOUNT_DUEDATE>
                                    <PAY_NO>~</PAY_NO>
                                    <DISCTRLIST>
                                    </DISCTRLIST>
                                    <DISCTRDELLIST>0</DISCTRDELLIST>
                                    <LINE_EXP>$DESCRIPTION</LINE_EXP>
                                </PAYMENT>
                                </PAYMENT_LIST>
                                <MONTH>$DATE_MONTH</MONTH>
                                <YEAR>$DATE_YEAR</YEAR>
                                <AFFECT_RISK>0</AFFECT_RISK>
                                <ORGLOGOID></ORGLOGOID>
                                <DOC_DATE>$DATE</DOC_DATE>
                                <DISTRIBUTION_TYPE_FNO>0</DISTRIBUTION_TYPE_FNO>
                                <PREACCLINES>
                                </PREACCLINES>
                                <GUID></GUID>
                            </TRANSACTION>
                            </ATTACHMENT_ARP>
                            <DOC_DATE>$DATE</DOC_DATE>
                            <TIME></TIME>
                            <GUID></GUID>
                            <DEFNFLDSLIST>
                            </DEFNFLDSLIST>
                            <LABEL_LIST>
                            </LABEL_LIST>
                        </SD_TRANSACTION>
                    </SD_TRANSACTIONS>
                XML;
            
            $request = $client->request('GET','http://'.$ip.':'.$port, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'LogoStatus' => 'SD_TRANSACTIONS',
                    'RequestType' => 'Logo',
                    'CompanyId' => $COMPANY_ID
                ],
                'body' => requestCrypt::requestEncrypted($cashPaymentXmlRequest)
            ]);

            return $request;
        } catch (\Throwable $th) {
            \Log::info("Logo Nakit Tahsilat Aktarılamadı : ".$th);
        }
    }
}