<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\License;
use App\Models\UserHasRole;
use App\Models\User;
use Illuminate\Support\Carbon;
use DB;

use Illuminate\Support\Facades\Log;
class invoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();

        $data = new Invoice;
        if ($request->transferStatus) {
            $data = $data->where('status', $request->transferStatus);
        }
        if ($request->company_id) {
            $data = $data->where('company_id', $request->company_id);
        }
        if ($request->typeOf) {
            if ($request->typeOf == 1) {
                $data = $data->where('type',1);
            }else {
                $data = $data->whereIn('type',[8,9]);
            }
        }
        if ($request->beginDate) {                
            $beginDate = Carbon::parse(str_replace('"','',$request->beginDate))->startOfDay()->format('Y-m-d H:i:s');
            $data = $data->where('created_at','>=', $beginDate);
        }
        if ($request->endDate) {                
            $endDate = Carbon::parse(str_replace('"','',$request->endDate))->endOfDay()->format('Y-m-d H:i:s');
            $data = $data->where('created_at','<=', $endDate);
        }
        $data = $data->orderBy('id','desc')->get();
        $invoiceQuery = collect(DB::select(
        "select
        sum(CASE WHEN status = 200 THEN 1 else 0 end) as 'successInvoice' ,
        sum(CASE WHEN status = 201 THEN 1 else 0 end) as 'failedInvoice' ,
        ROUND((sum(CASE WHEN status = 200 THEN 1 else 0 end) /  count(*)) * 100,0) as 'successInvoiceRate' ,
        count(*) as 'totalInvoice'  from invoices;"))[0];

        $data = $data->map(function($query){
            $query->type = $query->type == 1 ? 'Alış':'Satış';
            $query->logoStatus = $query->status == 200 ? 'Başarılı':'Başarısız';
            return $query;
        });

        return response()->json(['success'=>true,'user'=>$user,'data'=>$data,'count'=>$invoiceQuery]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }


}
