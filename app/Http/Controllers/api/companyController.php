<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\UserHasRole;
use App\Models\User;
class companyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code == 'superAdmin'){
            $companies = Company::get();
            return response()->json([
                'success'=>true,
                'data'=>$companies
            ]);
        }
        $companies = Company::where('userId',$user->id)->get();
        return response()->json([
            'success'=>true,
            'data'=>$companies
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();
        if ($userRole->role[0]->code == 'superAdmin') {
            $userId = $request->userId;
        }else {
            $userId = $user->id;
        }
        $create = Company::create([
            'userId'=>$user->id,
            'name'=>$request->name,
            'logoId'=>$request->logoId
        ]);
        if($create){
            return response()->json(['success'=>true]);
        }
        else {
            return response()->json(['success'=>false,'message'=>'Firma Eklenemedi']);
        }

    }

    public function multiStore(Request $request)
    {
        $user = request()->user();
        if ($request->selectedCompanies) {
            $companies = Company::where('licenseId',$request->licenseId)->delete();
            try {
                foreach ($request->selectedCompanies as $param) {
                    $create = Company::create([
                        'userId'=>$user->id,
                        'name'=>$param["name"],
                        'logoId'=>$param["logoId"],
                        'licenseId'=>$request->licenseId
                    ]);
                }
                return response()->json([
                    'success'=>true,
                    'message'=>'Firmalar Oluşturuldu'
                ],200);
            } catch (\Throwable $th) {
                return response()->json(['success'=>false,'message'=>'Firma Eklenemedi'.$th],401);
            }
        }else {
            return response()->json(['success'=>false,'message'=>'Geçersiz İstek'],401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktadır']);}
        $company = Company::where('id',$id)->first();
        return response()->json([
            'success'=>true,
            'company'=>$company
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktadır']);}
        $update = Company::where('id',$id)->update([
            'userId'=>$user->id,
            'name'=>$request->name,
            'logoId'=>$request->logoId
        ]);
        if($update){
            return response()->json(['success'=>true]);
        }
        else 
        {
            return response()->json(['success'=>false,'message'=>'Firma düzenlenemedi']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = request()->user();
        $roleId = UserHasRole::where('user_id',$user->id)->first();
        $role = Role::where('id',$roleId->role_id)->first();
        if($role->code != 'superAdmin') { return response()->json(['success'=>false,'message'=>'Yetkiniz bulunmamaktadır']);}
        Company::where('id',$id)->delete();
        return response()->json(['success'=>true,'message'=>'Firma Silindi']);
    }
}
