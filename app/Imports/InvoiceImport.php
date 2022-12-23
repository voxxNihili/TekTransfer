<?php

namespace App\Imports;

use App\Models\InvoiceExcel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Http\Controllers\LogoSalesController;
use App\Http\Controllers\LogoPurchaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\api\queryController;

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
            $req['invoiceNumber'] = null;
            $req['TaxNumber'] =str_replace('"','',$invoice[0]->vergi_numarasi);
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
            $req['companyId'] = 8;
            $req['cPnrNo'] = $this->logoCurrent($req['fullname'],$req['TaxNumber'],"20",$req['companyId']);
            $invoiceDetails = collect();

            foreach ($invoice as $detail) {
                $invoiceDetail = collect();
                $invoiceDetail['cDepo'] = $detail->depo_kodu;
                $invoiceDetail['Id'] = "";
                $invoiceDetail['price'] = $detail->yekun;
                $invoiceDetail['quantity'] = $detail->miktar;
                $invoiceDetail['taxRate'] = $detail->kdv_orani;
                $invoiceDetail['productName'] = $detail->stok_adi;
                $invoiceDetail['productCode'] = str_replace('"','',$detail->stok_no);
                $invoiceDetail['productBarcode'] = str_replace('"','',$detail->stok_no);
                $invoiceDetail['unit'] = $detail->birim;
                $invoiceDetail['description'] = $detail->aciklama;
                $invoiceDetail['type'] = 0;
                $invoiceDetails->push($invoiceDetail);
            }
            $req['invoiceDetails'] = $invoiceDetails;

            if ($invoiceImportRowData['turu'] == "satis") {
                $logoSalesController = new LogoSalesController;
                $reqSalesQuery = $logoSalesController->salesInvoice($req);
            }else {
                $logoPurchaseController = new LogoPurchaseController;
                $reqPurchaseQuery = $logoPurchaseController->purchaseInvoice($req);
            }

            $responseData = json_decode($reqSalesQuery->content());
            return $responseData;
        }
    }

    public function logoCurrent($currentName, $currentTaxNo, $licenseId, $companyId)
    {
        $req = new Request;
        $req['licenseId'] = $licenseId;
        $req['companyId'] = $companyId;
        $req['periodId'] = "01";
        $req['query'] =  ['**current**' => $currentName, '**tax**' => $currentTaxNo];
        $reqCode = 'search_taxno';
        $queryController = new queryController;
        $reqQuery = $queryController->generateQuery($req,$reqCode);
        $responseData = json_decode($reqQuery->content());

        if ($responseData->data) {
            return $responseData->data[0]->CODE;
        }else {
            
        }
    }

}