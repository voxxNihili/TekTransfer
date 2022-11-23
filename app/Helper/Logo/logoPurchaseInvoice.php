<?php 
namespace App\Helper\Logo;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Helper\requestCrypt;

class logoPurchaseInvoice
{
    static function purchaseInvoicePostData($params)
    {
        try {
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
            $LOCATION = $params['LOCATION'];
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
            $COMPANY_ID = $params['COMPANY_ID'];
            $NOTE = $params['NOTE'];

            $NOTE = substr($NOTE, 0, 300) . '';
            $noteArr = str_split($NOTE, 50);
            $noteLength = strlen($NOTE);     
            $NOTES1 = ' ';
            $NOTES2 = ' ';
            $NOTES3 = ' ';
            $NOTES4 = ' ';
            $NOTES5 = ' ';
            $NOTES6 = ' ';
            for($i=0; $i <ceil($noteLength/50) ; $i++) {
                ${"NOTES".$i+1} = $noteArr[$i];
            }

            $xmlRequest  = <<<XML
            <?xml version="1.0" encoding="ISO-8859-9"?>
                <PURCHASE_INVOICES>
                    <INVOICE DBOP="INS" >
                        <TYPE>$TYPE</TYPE>
                        <NUMBER>$NUMBER</NUMBER>
                        <DATE>$DATE</DATE>
                        <TIME>$TIME</TIME>
                        <AUXIL_CODE>$LOCATION</AUXIL_CODE>
                        <ARP_CODE>$ARP_CODE</ARP_CODE>
                        <POST_FLAGS>$POST_FLAGS</POST_FLAGS>
                        <VAT_RATE>$VAT_RATE</VAT_RATE>
                        <TOTAL_DISCOUNTED>$TOTAL_DISCOUNTED</TOTAL_DISCOUNTED>
                        <TOTAL_VAT>$TOTAL_VAT</TOTAL_VAT>
                        <TOTAL_GROSS>$TOTAL_GROSS</TOTAL_GROSS>
                        <TOTAL_NET>$TOTAL_NET</TOTAL_NET>
                        <NOTES1>$NOTES1</NOTES1>
                        <NOTES2>$NOTES2</NOTES2>
                        <NOTES3>$NOTES3</NOTES3>
                        <NOTES4>$NOTES4</NOTES4>
                        <NOTES5>$NOTES5</NOTES5>
                        <NOTES6>$NOTES6</NOTES6>
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
                </PURCHASE_INVOICES>
            XML;
            $request = $client->request('GET','http://'.$ip.':'.$port, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'LogoStatus' => 'PURCHASE_INVOICES',
                    'RequestType' => 'Logo',
                    'CompanyId' => $COMPANY_ID
                ],
                'body' => requestCrypt::requestEncrypted($xmlRequest)
            ]);
            return $request;
        } catch (\Throwable $th) {
            \Log::channel('logoPurchaseInvoice')->info("Alım Faturası Aktarılamadı");
        }
    }
}