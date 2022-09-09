<?php

namespace App\Http\Controllers\api\query;

use App\Http\Controllers\Controller;
use App\Models\Query;
use App\Models\QueryParameter;
use App\Models\QueriesHasParameters;
use Illuminate\Http\Request;
use App\Models\UserHasRole;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\License;
use GuzzleHttp\Exception\GuzzleException;
class indexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();

        if ($userRole->role[0]->code == 'superAdmin') {
            $data = Query::all();
        }else {
            return response()->json([
                'success'=>false,
                'message'=>'Yetkiniz Bulunmamaktadır.'
            ]);
        }
        return response()->json(['success'=>true,'user'=>$user,'data'=>$data]);
    }

    public function create(){}

    public function store(Request $request)
    {
        $all = $request->all();
        $all['name'] = $request->name;
        $all['code'] = $request->code;
        $all['sqlQuery'] = $request->sqlQuery;
        $create = Query::create($all);

        if($create){
            return response()->json([
                'success'=>true,
                'message'=>'Sorgu Oluşturuldu'
            ]);
        }
        else
        {
            return response()->json([
                'success'=>false,
                'message'=>'Sorgu Oluşturulamadı'
            ]);
        }
    }
    public function show($id){}

    public function edit($id){}

    public function update(Request $request, $id){}

    public function destroy($id){}

    public function generateQuery(Request $request, $code){

        $sqlData = Query::where('code',$code)->first();
        $sql = $sqlData->sqlQuery;
        $query = $request["query"][0];

        $license = License::where('licenseKey',$request->license)->with('logoSetting')->first();

        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            return response()->json([
                'success'=>false,
                'message'=>'Geçersiz Lisans!'
            ]);
        }

        $sqlQueryXXX = str_replace('**XXX**', $license['logoSetting'][0]->sqlPeriod, $sql);
        $sqlQueryXX = str_replace('**XX**', $license['logoSetting'][0]->sqlCompanyId, $sqlQueryXXX);
        $sqlQuery =  str_replace(array_keys($query), $query, $sqlQueryXX); 

        if (strstr($sqlQuery,'**')) {
            return response()->json([
                'success'=>false,
                'message'=>'Geçersiz Sorgu!'
            ]);
        }else {
            $client = new Client(['verify' => false]);
            $req = $client->request('GET','http://'.$ip.':'.$port, [
                'headers' => [
                    'LogoStatus' => '',
                    'RequestType' => 'Sql'
                ],
                'body' => $this->requestEncrypted($sqlQuery)
            ]);
            return response()->json([
                'success'=>true,
                'message'=>'Sorgu Başarılı',
                'data'=>json_decode(json_decode(html_entity_decode($req->getBody()->getContents()),true))
            ]);
        }
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