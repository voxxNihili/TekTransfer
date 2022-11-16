<?php 
namespace App\Helper\Logo;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Helper\requestCrypt;

class logoItem
{
    static function itemPostData($params, $ip, $port)
    {
        try {
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
                    'RequestType' => 'Logo',
                    'CompanyId' => '8'
                ],
                'body' => requestCrypt::requestEncrypted($itemXmlRequest)
            ]);    
            $response = $request->getBody()->getContents();    
            return $response;
        } catch (\Throwable $th) {
            \Log::channel('logoItem')->info("Logo Ürün/Hizmet Aktarılamadı : ".$th);
        }
    }
}