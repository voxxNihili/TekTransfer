<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\LogoExcelRequest;
use App\Http\Controllers\LogoSalesController;
use App\Http\Controllers\LogoPurchaseController;
class ExcelInvoiceQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExcelInvoice:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   

        $logoExcelRequest = new LogoExcelRequest;
        $logoExcelRequest = $logoExcelRequest->where('invoice_status',0)->first();

        if (condition) {
            $req = new Request;
            $req["manuel"] = $logoExcelRequest->request_data;
    
            if ($logoExcelRequest->type=="8") {
                $logoSalesController = new LogoSalesController;
                $reqQuery = $logoSalesController->salesInvoice($req);
            }elseif ($logoExcelRequest->type=="1") {
                $logoPurchaseController = new LogoPurchaseController;
                $reqQuery = $logoPurchaseController->purchaseInvoice($req);
            }
            $retunQuery = json_decode($reqQuery->content());
            
            if ($retunQuery->success == true) {
                $logoExcelRequest->invoice_status_message = "Aktarım Başarılı";
                $logoExcelRequest->invoice_status = 1;
                $logoExcelRequest->update(); 
            }else {
                $logoExcelRequest->invoice_status_message = "Aktarım Başarısız";
                $logoExcelRequest->invoice_status = 2;
                $logoExcelRequest->update(); 
            }
        }

    }
}
