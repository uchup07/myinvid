<?php

namespace App\Modules\Confirmation\Http\Controllers;

use App\Modules\Cashbook\Models\Cashbook;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;

use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Invoice\Models\Invoice as InvoiceModel;
use App\Modules\Setting\Models\Setting as SettingModel;
use App\Modules\Confirmation\Models\Confirmation as ConfirmationModel;
use App\Modules\Cashbook\Models\Cashbook as CashbookModel;


use Theme;
use Auth;
use Activity;
use Debugbar;


class ConfirmationController extends Controller
{



    public function get_detail(Request $request){
        $ConfirmationID                                 = $request->id;

        $Confirmation                                   = ConfirmationModel::find($ConfirmationID);

        if($Confirmation){
            if($Confirmation->approved == 1){
                $StatusApprove                          = "Pending";
            }else if($Confirmation->approved == 2){
                $StatusApprove                          = "Approve";
            }else if($Confirmation->approved == 3){
                $StatusApprove                          = "Reject";
            }
            $data                                       = array(
                "status"                                    => true,
                "output"                                    => array(
                    "no_invoice"                            => $Confirmation->invoice->invoice_number,
                    "name"                                  => $Confirmation->user->name,
                    "domain"                                => $Confirmation->invoice->domain,
                    "email"                                 => $Confirmation->user->email,
                    "tagihan_display"                       => number_format($Confirmation->invoice->total,0,",","."),
                    "tagihan"                               => $Confirmation->invoice->total,
                    "pengirim"                              => $Confirmation->name,
                    "no_rekening"                           => $Confirmation->account_of_bank,
                    "bank"                                  => $Confirmation->bank_information,
                    "nominal"                               => $Confirmation->total,
                    "nominal_display"                       => number_format($Confirmation->total,0,",","."),
                    "status_approve"                        => $Confirmation->approved,
                    "status_approve_display"                => $StatusApprove
                ),
                "message"                                   => 'OK'
            );
        }else{
            $data                                       = array(
                "status"                                    => false,
                "message"                                   => 'Data tidak ditemukan'
            );
        }
        return response($data, 200)->header('Content-Type', 'text/plain');
    }

    public function reject($ConfirmationID){
        $Confirmation                                   = ConfirmationModel::find($ConfirmationID);
        $Confirmation->approved                         = 3;
        try{
            $Confirmation->save();
            return redirect()
                ->route('invoice_verified_list')
                ->with('ScsMsg',"Admin berhasil menolak konfirmasi invoice ".$Confirmation->invoice->invoice_number);

        }catch (\Exception $e){
            $Details                                     = array(
                "user_id"                                   => Auth::id(),
                "confirmation_id"                           => $ConfirmationID,
                "approved"                                  => 3
            );

            Activity::log([
                'contentId'     => $ConfirmationID,
                'contentType'   => 'Confirmation Reject',
                'action'        => 'reject',
                'description'   => "Ada kesalahan teknis pada Update Status",
                'details'       => $e->getMessage(),
                'data'          => json_encode($Details),
                'updated'       => Auth::id(),
            ]);
            return redirect()
                ->route('invoice_verified_list')
                ->with('ErrMsg',"Ada kesalahan teknis. Mohon hubungi web development (barindra1988@gmail.com)");
        }
    }

    public function approve($ConfirmationID){
        $Confirmation                                   = ConfirmationModel::find($ConfirmationID);
        $Confirmation->approved                         = 2;
        $Confirmation->approved_by                      = Auth::id();
        $Confirmation->approved_at                      = date('Y-m-d H:i:s');
        $Total                                          = $Confirmation->total;

        try{
            $Confirmation->save();

            $CashBook                                   = new CashbookModel();
            $CashBook->debit                            = $Total;
            $CashBook->credit                           = 0;
            $CashBook->ref_id                           = $ConfirmationID;
            $CashBook->flow                             = "I";
            $CashBook->status                           = 1; # Status Transaction (Order)
            $CashBook->date_transaction                 = date('Y-m-d H:i:s');
            $CashBook->description                      = "Pembayaran Invoice " . $Confirmation->invoice->invoice_number;
            $CashBook->website_id                       = $Confirmation->website_id;
            $CashBook->user_id                          = $Confirmation->user_id;
            $CashBook->flag                             = 0; # Active
            $CashBook->url                              = route('invoice_show',$Confirmation->invoice_id);
            $CashBook->created_by                       = Auth::id();
            $CashBook->updated_by                       = Auth::id();

            try{
                $CashBook->save();
                $ArrSetSaldoAccount                     = array(
                    "id"                                => 1,
                    "total"                             => $Total,
                    "flow"                              => 'IN'
                );

                $SetSaldo                               = set_SaldoAccount($ArrSetSaldoAccount);
                if($SetSaldo){
                    $CashBookInfo                       = CashbookModel::find($CashBook->id);
                    $CashBookInfo->no_transaction       = "BM".date('Ymd').sprintf('%04s',$CashBook->id);
                    $CashBookInfo->save();


                    ### SET INVOICE LUNAS ###
                    $Invoice                            = InvoiceModel::find($Confirmation->invoice_id);
                    $Invoice->paid                      = 'Yes';
                    $Invoice->save();
                    ### SET INVOICE LUNAS ###

                    ### SET WEBSITE AKTIF ###
                    $Website                            = WebsiteModel::find($Confirmation->website_id);
                    $Website->is_active                 = 1;
                    $Website->is_active_date            = date('Y-m-d H:i:s');
                    $Website->approved_by               = Auth::id();
                    $Website->save();
                    ### SET WEBSITE AKTIF ###

                    return redirect()
                        ->route('invoice_verified_list')
                        ->with('ScsMsg',"Admin berhasil menerima konfirmasi invoice ".$Confirmation->invoice->invoice_number);
                }else{
                    Activity::log([
                        'contentId'     => $CashBook->id,
                        'contentType'   => 'Confirmation Approve',
                        'action'        => 'approve',
                        'description'   => "Ada kesalahan teknis pada Simpan cashbook/Saldo realtime",
                        'details'       => "Kesalahan pada set Saldo Acccount Realtime id = 1",
                        'data'          => json_encode($ArrSetSaldoAccount),
                        'updated'       => Auth::id(),
                    ]);

                    return redirect()
                        ->route('invoice_verified_list')
                        ->with('ScsMsg',"Admin berhasil menerima konfirmasi invoice ".$Confirmation->invoice->invoice_number);
                }

            }catch (\Exception $e){

                $Details                                     = array(
                    "debit"                                         => $Total,
                    "credit"                                        => 0,
                    "ref_id"                                        => $ConfirmationID,
                    "flow"                                          => "I",
                    "status"                                        => 1,
                    "date_transaction"                              => date('Y-m-d H:i:s'),
                    "description"                                   => "Pembayaran Invoice " . $Confirmation->invoice->invoice_number,
                    "website_id"                                    => $Confirmation->website_id,
                    "user_id"                                       => $Confirmation->user_id,
                    "flag"                                          => 0,
                    "url"                                           => route('invoice_show',$Confirmation->invoice_id),
                    "invoice_id"                                    => $Confirmation->invoice_id
                );

                Activity::log([
                    'contentId'     => 0,
                    'contentType'   => 'Confirmation Approve',
                    'action'        => 'approve',
                    'description'   => "Ada kesalahan teknis pada Simpan cashbook/Saldo realtime",
                    'details'       => $e->getMessage(),
                    'data'          => json_encode($Details),
                    'updated'       => Auth::id(),
                ]);

                return redirect()
                    ->route('invoice_verified_list')
                    ->with('ErrMsg',"Ada kesalahan teknis. Mohon hubungi web development (barindra1988@gmail.com)");

            }

        }catch (\Exception $e){
            $Details                                     = array(
                "user_id"                                   => Auth::id(),
                "confirmation_id"                           => $ConfirmationID,
                "approved"                                  => 2
            );

            Activity::log([
                'contentId'     => $ConfirmationID,
                'contentType'   => 'Confirmation Approve',
                'action'        => 'approve',
                'description'   => "Ada kesalahan teknis pada Update Status",
                'details'       => $e->getMessage(),
                'data'          => json_encode($Details),
                'updated'       => Auth::id(),
            ]);
            return redirect()
                ->route('invoice_verified_list')
                ->with('ErrMsg',"Ada kesalahan teknis. Mohon hubungi web development (barindra1988@gmail.com)");
        }


    }
}
