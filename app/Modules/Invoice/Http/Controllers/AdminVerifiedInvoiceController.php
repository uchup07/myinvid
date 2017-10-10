<?php

namespace App\Modules\Invoice\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Invoice\Models\Invoice as InvoiceModel;
use App\Modules\Setting\Models\Setting as SettingModel;
use App\Modules\Confirmation\Models\Confirmation as ConfirmationModel;


use Theme;
use Auth;
use Activity;
use Debugbar;

class AdminVerifiedInvoiceController extends Controller
{

    protected $_data                                = array();

    public function __construct()
    {
        $this->middleware('permission:transaction-view');
        $this->middleware('permission:invoice-verified-view');
        $this->_data['string_menuname']             = 'Invoice';
    }

    public function datatables(){
        $UserID                                     = Auth::id();

        $Confirmation = ConfirmationModel::join('websites','websites.id','=','confirmations.website_id')
                                        ->join('invoices','invoices.id','=','confirmations.invoice_id')
                                        ->select(['confirmations.id','invoices.id as InvoiceID','invoices.invoice_number','confirmations.date_transaction','websites.domain','confirmations.approved'])
                                        ->orderBy('confirmations.approved','ASC');

        return Datatables::of($Confirmation)
            ->addColumn('href', function ($Confirmation) {
                return '
                        <button class="btn green btn-circle" onclick="ConfirmVerifiedNow('.$Confirmation->id.')"><i class="fa fa-check-circle"></i></button>
                        ';
            })


            ->editColumn('invoice_number', function ($Confirmation) {
                    return '<a href="'.route('invoice_show',$Confirmation->InvoiceID).'" target="_blank">'.$Confirmation->invoice_number.'</a>';
            })

            ->editColumn('approved', function ($Confirmation) {
                if($Confirmation->approved == 1){ # Pending #
                    return '<span class="label label-sm label-warning">'.$Confirmation->status_approve->name.'</span>';
                }else if($Confirmation->approved == 2){
                    return '<span class="label label-sm label-success">'.$Confirmation->status_approve->name.'</span>';
                }else if($Confirmation->approved == 3){
                    return '<span class="label label-sm label-danger">'.$Confirmation->status_approve->name.'</span>';
                }
            })

            ->editColumn('date_transaction', function ($Confirmation) {
                    return DateFormat($Confirmation->date_transaction,"d/m/Y");
            })


            ->rawColumns(['href','invoice_number','approved'])
            ->make(true);
    }

    public function listInvoice(){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Daftar Invoice';
        $this->_data['DateNow']                     = date('d-m-Y');

        return Theme::view('modules.invoice.verified.list',$this->_data);
    }




}
