<?php 
namespace App\Helper\Logo;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Helper\requestCrypt;

class logoCreditCard
{
    static function creditCardPostData($params)
    {
        try {
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

            $creditCardXmlRequest  = <<<XML
                <?xml version="1.0" encoding="ISO-8859-9"?>
                <ARP_VOUCHERS>
                    <ARP_VOUCHER DBOP="INS" >
                        <INTERNAL_REFERENCE>1</INTERNAL_REFERENCE>
                        <NUMBER>00000001</NUMBER>
                        <DATE>16.11.2022</DATE>
                        <TYPE>70</TYPE>
                        <DEPARTMENT>2</DEPARTMENT>
                        <TOTAL_CREDIT>1000</TOTAL_CREDIT>
                        <CREATED_BY>120</CREATED_BY>
                        <DATE_CREATED>16.11.2022</DATE_CREATED>
                        <HOUR_CREATED>13</HOUR_CREATED>
                        <MIN_CREATED>45</MIN_CREATED>
                        <SEC_CREATED>19</SEC_CREATED>
                        <CURRSEL_TOTALS>1</CURRSEL_TOTALS>
                        <DATA_REFERENCE>1</DATA_REFERENCE>
                        <TRANSACTIONS>
                            <TRANSACTION>
                                <INTERNAL_REFERENCE>14</INTERNAL_REFERENCE>
                                <ARP_CODE>120.01.O02</ARP_CODE>
                                <TRANNO>00000001</TRANNO>
                                <CREDIT>1000</CREDIT>
                                <TC_AMOUNT>1000</TC_AMOUNT>
                                <BNLN_TC_AMOUNT>1000</BNLN_TC_AMOUNT>
                                <PAYMENT_LIST>
                                    <PAYMENT>
                                        <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                                        <DATE>16.11.2022</DATE>
                                        <MODULENR>5</MODULENR>
                                        <SIGN>1</SIGN>
                                        <TRCODE>70</TRCODE>
                                        <TOTAL>1000</TOTAL>
                                        <PROCDATE>16.11.2022</PROCDATE>
                                        <DATA_REFERENCE>0</DATA_REFERENCE>
                                        <DISCOUNT_DUEDATE>16.11.2022</DISCOUNT_DUEDATE>
                                        <PAY_NO>1</PAY_NO>
                                        <DISCTRLIST>
                                        </DISCTRLIST>
                                        <DISCTRDELLIST>0</DISCTRDELLIST>
                                    </PAYMENT>
                                </PAYMENT_LIST>
                                <DATA_REFERENCE>14</DATA_REFERENCE>
                                <MONTH>11</MONTH>
                                <YEAR>2022</YEAR>
                                <AFFECT_RISK>0</AFFECT_RISK>
                                <ORGLOGOID></ORGLOGOID>
                                <BANKACC_CODE>01    102.01.02</BANKACC_CODE>
                                <DISTRIBUTION_TYPE_FNO>0</DISTRIBUTION_TYPE_FNO>
                                <DEFNFLDSLIST>
                                </DEFNFLDSLIST>
                                <PREACCLINES>
                                </PREACCLINES>
                                <GUID>0C41D874-55A5-46A4-9FAF-D083F1F1B8C1</GUID>
                            </TRANSACTION>
                        </TRANSACTIONS>
                        <ARP_CODE>120.01.O02</ARP_CODE>
                        <TIME>220990487</TIME>
                        <BANKACC_CODE>01    102.01.02</BANKACC_CODE>
                        <AFFECT_RISK>0</AFFECT_RISK>
                        <GUID>33674EBF-1A89-4F3B-803C-C67A8348C3ED</GUID>
                        <DEFNFLDSLIST>
                        </DEFNFLDSLIST>
                        <LABEL_LIST>
                        </LABEL_LIST>
                    </ARP_VOUCHER>
                </ARP_VOUCHERS>
            XML;
            
            $request = $client->request('GET','http://'.$ip.':'.$port, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'LogoStatus' => 'ARP_VOUCHERS',
                    'RequestType' => 'Logo',
                    'CompanyId' => '8'
                ],
                'body' => requestCrypt::requestEncrypted($creditCardXmlRequest)
            ]);
            $response = $request->getBody()->getContents();
            return $response;
        } catch (\Throwable $th) {
            \Log::channel('logoCurrent')->info("Logo Kredi Kartı Tahsilatı Aktarılamadı : ".$th);
        }
    }
}