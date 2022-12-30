<?php 
namespace App\Helper\Logo;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Helper\requestCrypt;

class logoPayment
{
    static function paymentPostData($params)
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
            $BANKACC_CODE = $params['BANKACC_CODE'];
            $COMPANY_ID = $params['COMPANY_ID'];
            $DESCRIPTION = $params['DESCRIPTION'];

            $DESCRIPTION = substr($DESCRIPTION, 0, 200) . '';
            $noteArr = str_split($DESCRIPTION, 50);
            $noteLength = strlen($DESCRIPTION);     
            $NOTES1 = ' ';
            $NOTES2 = ' ';
            $NOTES3 = ' ';
            $NOTES4 = ' ';
            for($i=0; $i <ceil($noteLength/50) ; $i++) {
                ${"NOTES".$i+1} = $noteArr[$i];
            }

            $paymentXmlRequest  = <<<XML
                <?xml version="1.0" encoding="ISO-8859-9"?>
                <ARP_VOUCHERS>
                    <ARP_VOUCHER DBOP="INS" >
                        <INTERNAL_REFERENCE>1</INTERNAL_REFERENCE>
                        <NUMBER>~</NUMBER>
                        <DATE>$DATE</DATE>
                        <TYPE>$TYPE</TYPE>
                        <DEPARTMENT>$DEPARTMENT</DEPARTMENT>
                        <TOTAL_CREDIT>$TOTAL</TOTAL_CREDIT>
                        <CURRSEL_TOTALS>1</CURRSEL_TOTALS>
                        <DATA_REFERENCE>1</DATA_REFERENCE>
                        <NOTES1>$NOTES1</NOTES1>
                        <NOTES2>$NOTES2</NOTES2>
                        <NOTES3>$NOTES3</NOTES3>
                        <NOTES4>$NOTES4</NOTES4>
                        <TRANSACTIONS>
                            <TRANSACTION>
                                <INTERNAL_REFERENCE>14</INTERNAL_REFERENCE>
                                <ARP_CODE>$ARP_CODE</ARP_CODE>
                                <TRANNO>00000001</TRANNO>
                                <CREDIT>$TOTAL</CREDIT>
                                <TC_AMOUNT>$TOTAL</TC_AMOUNT>
                                <BNLN_TC_AMOUNT>$TOTAL</BNLN_TC_AMOUNT>
                                <PAYMENT_LIST>
                                    <PAYMENT>
                                        <INTERNAL_REFERENCE>0</INTERNAL_REFERENCE>
                                        <DATE>$DATE</DATE>
                                        <MODULENR>5</MODULENR>
                                        <SIGN>1</SIGN>
                                        <TRCODE>70</TRCODE>
                                        <TOTAL>$TOTAL</TOTAL>
                                        <PROCDATE>$DATE</PROCDATE>
                                        <DATA_REFERENCE>0</DATA_REFERENCE>
                                        <DISCOUNT_DUEDATE>$DATE</DISCOUNT_DUEDATE>
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
                                <BANKACC_CODE>$BANKACC_CODE</BANKACC_CODE>
                                <DISTRIBUTION_TYPE_FNO>0</DISTRIBUTION_TYPE_FNO>
                                <DEFNFLDSLIST>
                                </DEFNFLDSLIST>
                                <PREACCLINES>
                                </PREACCLINES>
                                <GUID>0C41D874-55A5-46A4-9FAF-D083F1F1B8C1</GUID>
                            </TRANSACTION>
                        </TRANSACTIONS>
                        <ARP_CODE>$ARP_CODE</ARP_CODE>
                        <TIME>220990487</TIME>
                        <BANKACC_CODE>$BANKACC_CODE</BANKACC_CODE>
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
                    'CompanyId' => $COMPANY_ID
                ],
                'body' => requestCrypt::requestEncrypted($paymentXmlRequest)
            ]);

            return $request;
        } catch (\Throwable $th) {
            \Log::channel('logoCurrent')->info("Logo Tahsilat Aktarılamadı : ".$th);
        }
    }
}