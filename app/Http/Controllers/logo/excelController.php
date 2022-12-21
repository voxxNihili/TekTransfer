<?php

namespace App\Http\Controllers\logo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserHasRole;
use App\Models\Role;
use App\Imports\InvoiceImport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Excel;


class excelController extends Controller
{
        public function uploadInvoice(Request $request)
        {
                $result = Excel::import(new InvoiceImport, $request->File);
                
                if ($result) {
                        return response()->json([
                                'success'=>true,
                                'message'=>'Faturalar Aktarıldı!'
                                ],200);
                } else {
                        return response()->json([
                                'success'=>false,
                                'message'=>'Fatular Aktarılamadı!'
                                ],201);
                }
        }

}
