<?php

namespace App\Imports;

use App\Models\InvoiceExcel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Http\Controllers\LogoSalesController;
use App\Http\Controllers\LogoPurchaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\api\queryController;
use App\Helper\Logo\logoCurrent;
use App\Models\License;
use App\Http\Controllers\api\excelInvoiceController;
use App\Models\LogoExcelRequest;
use Illuminate\Support\Carbon;

class InvoiceImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift();
        $invoiceImportData = collect();
        foreach ($rows as $row) 
        {
            $invoiceImportRowData = collect();
            $invoiceImportRowData['turu'] = $row[0];
            $invoiceImportRowData['tarih'] = $row[1];
            $invoiceImportRowData['vade_tarih'] = $row[2];
            $invoiceImportRowData['durumu'] = $row[3];
            $invoiceImportRowData['evrak_turu'] = $row[4];
            $invoiceImportRowData['evrak_numarasi'] = $row[5];
            $invoiceImportRowData['cari_adi'] = $row[6];
            $invoiceImportRowData['vergi_numarasi'] = $row[7];
            $invoiceImportRowData['vergi_dairesi'] = $row[8];
            $invoiceImportRowData['cari_adresi'] = $row[9];
            $invoiceImportRowData['il'] = $row[10];
            $invoiceImportRowData['ilce'] = $row[11];
            $invoiceImportRowData['e_mail'] = $row[12];
            $invoiceImportRowData['stok_no'] = $row[13];
            $invoiceImportRowData['uretici'] = $row[14];
            $invoiceImportRowData['stok_adi'] = $row[15];
            $invoiceImportRowData['urun_aciklama'] = $row[16];
            $invoiceImportRowData['raf'] = $row[17];
            $invoiceImportRowData['kdv_orani'] = $row[18];
            $invoiceImportRowData['miktar'] = $row[19];
            $invoiceImportRowData['iskonto_oran'] = $row[20];
            $invoiceImportRowData['birim'] = $row[21];
            $invoiceImportRowData['fiyat'] = $row[22];
            $invoiceImportRowData['iskonto'] = $row[23];
            $invoiceImportRowData['ara_toplam'] = $row[24];
            $invoiceImportRowData['kdv_toplami'] = $row[25];
            $invoiceImportRowData['yekun'] = $row[26];
            $invoiceImportRowData['plaka'] = $row[27];
            $invoiceImportRowData['km'] = $row[28];
            $invoiceImportRowData['sase_no'] = $row[29];
            $invoiceImportRowData['ozel_kod'] = $row[30];
            $invoiceImportRowData['aciklama'] = $row[31];
            $invoiceImportRowData['depo_kodu'] = $row[32];
            $invoiceImportRowData['sube'] = $row[33];
            $invoiceImportRowData['satis_tipi'] = $row[34];
            $invoiceImportRowData['hesap'] = $row[35];
            $invoiceImportData->push($invoiceImportRowData);
        }
        $invoiceErrors = "";
        $invoiceImportData = json_decode($invoiceImportData->groupBy('evrak_numarasi'));

        foreach ($invoiceImportData as $invoice) {
            $req = new Request;
            if ($invoiceImportRowData['turu'] == "satis") {
                $req['type'] = 8;
            }elseif($invoiceImportRowData['turu'] == "alis"){
                $req['type'] = 1;
            }else {
                throw new \Exception("Fatura Türü Hatalı!");
            }
            $req['currencyRate'] = 1;
            $req['currency'] = "TL";
            $req['invoiceNumber'] = str_replace('"','',$invoice[0]->evrak_numarasi);
            $req['TaxNumber'] = str_replace('"','',$invoice[0]->vergi_numarasi);
            $req['TaxAuthority'] = $invoice[0]->vergi_dairesi;
            $req['address'] = $invoice[0]->cari_adresi;
            $req['Telephone'] = "";
            $req['city'] = $invoice[0]->il;
            $req['companyTitle'] = $invoice[0]->cari_adi;
            $req['district'] = $invoice[0]->ilce;
            $req['fullname'] = $invoice[0]->cari_adi;
            $req['personalIdentification'] = "";
            $req['invoiceDate'] = $invoice[0]->tarih;
            $req['note'] = $invoice[0]->aciklama;
            $req['name'] = $invoice[0]->cari_adi;
            $req['surname'] = $invoice[0]->cari_adi;
            $req['email'] = $invoice[0]->e_mail;
            $req['country'] = 'Türkiye';
            $req['cBeyannameNo'] = "";
            $req['noteEFatura'] = ".";
            $req['nSatisTipi'] = "";
            $req['leFatura'] = false;
            $req['licenseKey'] = "MNKCF-8HV9R-ALK2D-LHC4B";
            $req['companyId'] = "10";
            $currentDetails = collect();
            $currentDetails['name'] = $invoice[0]->cari_adi;
            $currentDetails['email'] = $invoice[0]->e_mail;
            $currentDetails['taxNo'] = str_replace('"','',$invoice[0]->vergi_numarasi);
            $currentDetails['taxOffice'] = $invoice[0]->vergi_dairesi;
            $currentDetails['address'] = $invoice[0]->cari_adresi;
            $currentDetails['city'] = $invoice[0]->il;
            $currentDetails['district'] = $invoice[0]->ilce;
            $currentDetails['country'] = 'Türkiye';
            $req['cPnrNo'] = $this->logoCurrent($req['licenseKey'],$req['companyId'],$currentDetails);
            $invoiceDetails = collect();

            foreach ($invoice as $detail) {
                $invoiceDetail = collect();
                $invoiceDetail['cDepo'] = $detail->depo_kodu;
                $invoiceDetail['Id'] = "";
                $invoiceDetail['price'] = round($detail->ara_toplam/$detail->miktar,4);
                $invoiceDetail['quantity'] = $detail->miktar;
                $invoiceDetail['taxRate'] = $detail->kdv_orani;
                $invoiceDetail['productName'] = $detail->stok_adi;
                $invoiceDetail['productCode'] = str_replace('"','',$detail->stok_no);
                $invoiceDetail['productBarcode'] = str_replace('"','',$detail->stok_no);
                $invoiceDetail['unit'] = $detail->birim;
                $invoiceDetail['description'] = str_replace('"','',$invoice[0]->evrak_numarasi)." ".$detail->aciklama;
                $invoiceDetail['type'] = 0;
                $invoiceDetails->push($invoiceDetail);
            }
            $req['invoiceDetails'] = $invoiceDetails;

            $user = request()->user();
            $logoExcelRequest = new LogoExcelRequest;
            $logoExcelRequest->created_by = $user->id;
            $logoExcelRequest->request_data = json_encode($req->all(), JSON_UNESCAPED_UNICODE);
            $logoExcelRequest->invoice_number = $req->invoiceNumber;
            $logoExcelRequest->invoice_date = Carbon::parse($req->invoiceDate)->format('Y-m-d H:i:s');
            $logoExcelRequest->current = $req->cPnrNo;
            $logoExcelRequest->customer_name = $req->fullname;
            $logoExcelRequest->company_name = 'OTORİNGO';
            $logoExcelRequest->company_id = $req->companyId;
            $logoExcelRequest->type = $req->type;
            $logoExcelRequest->invoice_status_message = 'Aktarım Bekleniyor';
            $logoExcelRequest->invoice_status = 0;
            $logoExcelRequest->save();
            
        }
    }

    public function logoCurrent($licenseKey, $companyId,$currentDetails)
    {
        $license = License::where('licenseKey',$licenseKey)->first();
        if ($license) {
            $ip = $license->ip;
            $port = $license->port;
        }else {
            throw new \Exception("Geçersiz Ürün Anahtarı!",500);
        }
 
        $req = new Request;
        $req['licenseId'] = $license->id;
        $req['companyId'] = $companyId;
        $req['periodId'] = "01";
        $req['query'] =  ['**current**' => $currentDetails["name"], '**tax**' => $currentDetails["taxNo"]];
        $reqCode = 'search_taxno';
        $queryController = new queryController;
        $reqQuery = $queryController->generateQuery($req,$reqCode);
        $responseData = json_decode($reqQuery->content());

        if ($responseData->data) {
            return $responseData->data[0]->CODE;
        }else {
            $currentCreateReq = new Request;
            $currentCreateReq['licenseId'] = $license->id;
            $currentCreateReq['companyId'] = $companyId;
            $currentCreateReq['periodId'] = "01";
            $currentCreateReq['query'] =  ['**arpPrefix**' => "120.01."];
            $currentCreateReqCode = 'last_current_logo_code';
            $currentQueryController = new queryController;
            $currentCreateReqQuery = $currentQueryController->generateQuery($currentCreateReq,$currentCreateReqCode);
            $currentCreateResponseData = json_decode($currentCreateReqQuery->content());
            
            if ($currentCreateResponseData->data) {
                //cari oluştur
                try {
                    $currentParams = array();
                    $currentParams['IP'] = $ip;
                    $currentParams['PORT'] = $port;
                    $currentParams['ACCOUNT_TYPE'] = 3; // ??????
                    $currentParams['CODE'] = $currentCreateResponseData->data[0]->Column1;
                    $currentParams['TITLE'] = $currentDetails["name"];
                    $currentParams['ADDRESS'] =  $currentDetails["address"];
                    $currentParams['DISTRICT'] = $currentDetails["district"];
                    $currentParams['CITY'] = $currentDetails["city"];
                    $currentParams['COUNTRY'] = $currentDetails["country"];
                    $currentParams['TELEPHONE'] = " ";
                    $currentParams['NAME'] = $currentDetails["name"];
                    $currentParams['SURNAME'] = "";
                    $currentParams['E_MAIL'] = $currentDetails["email"];
                    $currentParams['TCKNO'] = null;
                    $currentParams['TAX_ID'] = $currentDetails["taxNo"];
                    $currentParams['TAX_OFFICE'] = $currentDetails["taxOffice"];
                    $currentParams['COMPANY_ID'] = $companyId;
                    $responseCurrent = logoCurrent::currentPostData($currentParams);
                    
                    if ($responseCurrent->getStatusCode() == 200) {
                        return $currentCreateResponseData->data[0]->Column1;
                    }else {
                        throw new \Exception("Cari Oluşturulamadı. Fatura Türü Hatalı!",500);
                    }

                } catch (\Throwable $th) {
                    throw new \Exception("Cari Oluşturulamadı. Fatura Türü Hatalı! : ".$th,500);
                }
            }else {
                throw new \Exception("Sistemsel Hata. Logo Son Cari Kodu Sorgusu!",500);
            }
        }
    }

}