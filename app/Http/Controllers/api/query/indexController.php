<?php

namespace App\Http\Controllers\api\query;

use App\Http\Controllers\Controller;
use App\Models\QueryParameter;
use Illuminate\Http\Request;
use App\Models\UserHasRole;
use Illuminate\Support\Facades\Log;
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

}