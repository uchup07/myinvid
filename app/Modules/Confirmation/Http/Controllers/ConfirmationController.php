<?php

namespace App\Modules\Confirmation\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;

use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Invoice\Models\Invoice as InvoiceModel;
use App\Modules\Setting\Models\Setting as SettingModel;
use App\Modules\Confirmation\Models\Confirmation as ConfirmationModel;


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
                ->with('ScsMsg',"Admin berhasil menolak invoice ".$Confirmation->invoice->invoice_number);

        }catch (\Exception $e){
            $Details                                     = array(
                "user_id"                                   => Auth::id(),
                "confirmation_id"                           => $ConfirmationID
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
                ->with('ErrMsg',"Ada kesalahan teknis. Mohon hubungi web development (barindra1988@Gmail.com)");
        }
    }

    public function approve($ConfirmationID){
        $Confirmation                                   = ConfirmationModel::find($ConfirmationID);
        dd($Confirmation);

    }
}
