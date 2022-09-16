<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\QueryParameter;
use App\Models\UserHasRole;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class queryParameterController extends Controller
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
            $data = QueryParameter::all();
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
        $all['parameter'] = $request->parameter;
        $all['name'] = $request->name;
        $all['data_type'] = $request->data_type;

        $create = QueryParameter::create($all);

        if($create){
            return response()->json([
                'success'=>true,
                'message'=>'Sorgu Parametresi Oluşturuldu'
            ]);
        }
        else
        {
            return response()->json([
                'success'=>false,
                'message'=>'Sorgu Parametresi Oluşturulamadı'
            ]);
        }
    }
    public function show($id){
        $queryParameter = QueryParameter::where('id',$id)->first();
        return response()->json([
            'success'=>true,
            'query'=>$queryParameter
        ]);
    }

    public function edit($id){}

    public function update(Request $request, $id){
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        $control = QueryParameter::where('id',$id)->count();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Sorgu Parametresi Güncellenemedi, Yetkiniz bulunmamaktadır']);}
        $update = QueryParameter::where('id',$id)->update([
            'parameter'=>$request->parameter,
            'name'=>$request->name
        ]);
        if($update){
            return response()->json(['success'=>true,'message'=>'Sorgu Parametresi Güncellendi']);
        }else{return response()->json(['success'=>false,'message'=>'Sorgu Parametresi Güncellenemedi']);}
    }
    public function destroy($id){
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktadır']);}
        QueryParameter::where('id',$id)->delete();
        return response()->json(['success'=>true,'message'=>'Sorgu Parametresi Silindi']);
    }

}
