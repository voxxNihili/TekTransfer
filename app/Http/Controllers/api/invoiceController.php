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
    public function index()
    {
        $user = request()->user();
        $userRole = UserHasRole::where('user_id',$user->id)->with('role')->first();

        if ($userRole->role[0]->code == 'superAdmin') {
            $data = Invoice::orderBy('id','desc')->get();

            $successInvoice = Invoice::where('status','200')->get()->count();
            $failedInvoice = Invoice::where('status','201')->get()->count();
            $totalInvoice = Invoice::get()->count();

            $invoiceQuery = collect(DB::select(
            "select
            sum(CASE WHEN status = 200 THEN 1 else 0 end) as 'successInvoice' ,
            sum(CASE WHEN status = 201 THEN 1 else 0 end) as 'failedInvoice' ,
            ROUND((sum(CASE WHEN status = 200 THEN 1 else 0 end) /  count(*)) * 100,0) as 'successInvoiceRate' ,
            count(*) as 'totalInvoice'  from invoices;"))[0];

            $data = $data->map(function($query){
                $query->type = $query->type == 1 ? 'Alış':'Satış';
                return $query;
            });
        }

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
