<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Query;
use App\Models\QueryParameter;
use App\Models\QueriesHasParameters;
use Illuminate\Http\Request;
use App\Models\UserHasRole;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\License;
use GuzzleHttp\Exception\GuzzleException;
use App\Helper\requestCrypt;
use Illuminate\Support\Carbon;

class queryController extends Controller
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
            $data = Query::with('queryParam.parameter')->orderByDesc('id')->get();
        }else {
            return response()->json([
                'success'=>false,
                'message'=>'Yetkiniz Bulunmamaktadır.'
            ]);
        }
        return response()->json(['success'=>true,'user'=>$user,'data'=>$data]);
    }

    public function reportShow($code)
    {
        $user = request()->user();
        $now = Carbon::now('Europe/Istanbul');
        $data = Query::where('code',$code)->with('queryParam.parameter')->get();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();
        if ($userRole->role[0]->code == 'superAdmin') {
            $licenses = License::where('endDate','>=',$now)->with('company')->get();
        }else {
            $licenses = License::where('userId',$user->id)->where('endDate','>=',$now)->with('company')->get();
        }
        $data = $data->map(function($query){
            $query->sqlQuery = '***';
            return $query;
        });
        return response()->json(['success'=>true,'user'=>$user,'data'=>$data,'licenses'=>$licenses]);
    }


    public function create(){}

    public function store(Request $request)
    {
        $query = new Query;
        $query->name = $request->name;
        $query->code = $request->code;
        $query->sqlQuery = $request->sqlQuery;
        $query->save();
        if($query){
            foreach ($request->selectedRows as $parameter) {
                $QueriesHasParameter = new QueriesHasParameters([
                    'query_id'=>$query->id,
                    'parameter_id'=>$parameter['id']
                ]);
                $QueriesHasParameter = $QueriesHasParameter->save();
            }
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
    public function show($id){
        $query = Query::where('id',$id)->with('queryParam.parameter')->first();
        return response()->json([
            'success'=>true,
            'query'=>$query
        ]);
    }

    public function edit($id){}

    public function update(Request $request, $id){
        $user = request()->user();

        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();

        $control = Query::where('id',$id)->count();

        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Sorgu Güncellenemedi, Yetkiniz bulunmamaktadır']);}

        $update = Query::where('id',$id)->update([
            'name'=>$request->name,
            'code'=>$request->code,
            'sqlQuery'=>$request->sqlQuery
        ]);

        if($update){
            $queryParameters = QueriesHasParameters::where('query_id', $id)->get();
            if($queryParameters){
                foreach ($queryParameters as $qParam) {
                    $qParam->delete();
                }
            }
            foreach ($request->selectedRows as $parameter) {
                $QueriesHasParameter = new QueriesHasParameters([
                    'query_id'=>$id,
                    'parameter_id'=>$parameter['id']
                ]);
                $QueriesHasParameter = $QueriesHasParameter->save();
            }
            return response()->json(['success'=>true,'message'=>'Sorgu Güncellendi']);
        }else{
            return response()->json(['success'=>false,'message'=>'Sorgu Güncellenemedi']);
        }
    }

    public function destroy($id){
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktadır']);}

        $queryParameters = QueriesHasParameters::where('query_id', $id)->get();

        if($queryParameters){
            foreach ($queryParameters as $qParam) {
                $qParam->delete();
            }
        } 

        Query::where('id',$id)->delete();
        return response()->json(['success'=>true,'message'=>'Sorgu Silindi']);
    }

    public function generateQuery(Request $request, $code){    
        if ($request->licenseId){
            $reqLicense = $request->licenseId;
            $reqQuery = $request["query"];
            $reqCompanyId = $request->companyId;
            $reqCompanyPeriodId = $request->periodId;
        }else {
            $reqAll = $request->request->all();
            $reqLicense = $reqAll['licenseId'];
            $reqQuery = $reqAll["query"];
            $reqCompanyId = $reqAll["companyId"];
            $reqCompanyPeriodId = $reqAll["periodId"];
        }
        $sqlData = Query::where('code',$code)->first();
        $sql = $sqlData->sqlQuery;
        $license = License::where('id',$reqLicense)->with('logoSetting')->first();

        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            return response()->json([
                'success'=>false,
                'message'=>'Geçersiz Lisans!'
            ]);
        }
        for ($i=strlen($reqCompanyId); $i < 3; $i++) {
            $reqCompanyId = "0".$reqCompanyId;
        }
        $sqlQueryXXX = str_replace('**XXX**', $reqCompanyId, $sql);
        $sqlQueryXX = str_replace('**XX**', $reqCompanyPeriodId, $sqlQueryXXX);        
        $sqlQuery =  str_replace(array_keys($reqQuery), $reqQuery, $sqlQueryXX);
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
                    'RequestType' => 'Sql',
                    'CompanyId' => '8'
                ],
                'body' => requestCrypt::requestEncrypted($sqlQuery)
            ]);
            return response()->json([
                'success'=>true,
                'message'=>'Sorgu Başarılı',
                'data'=>json_decode(json_decode(html_entity_decode($req->getBody()->getContents()),true))
            ]);
        }
    }

    public function showLogoCompanies($licenseId){
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();
        $companies = Company::where('licenseId',$licenseId)->get();
        if ($userRole->role[0]->code != 'superAdmin') {
            $license = License::where('userId',$user->id)->where('id',$licenseId)->first();
            if (!$license) {
                return response()->json([
                    'success'=>false,
                    'message'=>"Kullanıcıza ait firma bulunmamaktadır."
                ],201);
            }
        }
        return response()->json([
            'success'=>true,
            'companies'=>$companies
        ],200);
    }

    public function showLogoPeriods($companyId){
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();
        $company = Company::where('id',$companyId)->first();
        $license = License::where('id',$company->licenseId)->first();
        $req = new Request;
        $req->request->add(['licenseId' => $license->id]);
        $req->request->add(['companyId' => $company->logoId]);
        $req->request->add(['periodId' => "01"]);
        $req->request->add(['query' => ['**company**'=>$company->logoId]]);
        $reqCode = 'period';
        $periods = $this->generateQuery($req,$reqCode);
        $response = json_decode($periods->content());
        $responseData = collect($response->data);
        if ($userRole->role[0]->code != 'superAdmin') {
            $license = License::where('userId',$user->id)->where('id',$company->licenseId)->first();
            if (!$license) {
                return response()->json([
                    'success'=>false,
                    'message'=>"Kullanıcıza Ait Firma Dönemi Bulunmamaktadır."
                ],201);
            }
        }
        return response()->json([
            'success'=>true,
            'logoCompanyPeriods'=>$responseData
        ],200);
    }

}