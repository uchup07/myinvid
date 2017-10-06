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


use Theme;
use Auth;
use Activity;
use Debugbar;


class InvoiceController extends Controller
{
    protected $_data                                = array();

    public function __construct()
    {
        $this->middleware('permission:transaction-view');
        $this->_data['string_menuname']             = 'Invoice';
    }

    public function datatables(){
        $UserID                                     = Auth::id();

        $Invoice = InvoiceModel::select(['id', 'website_id','invoice_number', 'domain','date_expired','total','paid'])
            ->where('user_id','=',$UserID);

        return Datatables::of($Invoice)
            ->addColumn('href', function ($Invoice) {
                return '<a href="'.route('invoice_show',$Invoice->id).'" class="btn btn-info btn-circle"><i class="fa fa-search"></i></a>
                        <button class="btn green btn-circle" onclick="ConfirmNow('.$Invoice->id.')"><i class="fa fa-check-circle"></i></button>
                        <a class="btn yellow btn-circle" href="'.route("manage_wedding_information_detail",$Invoice->website_id).'"><i class="fa fa-edit"></i></a>
                        ';
            })

            ->editColumn('date_expired', function ($Invoice) {
                if($Invoice->paid == 'Yes'){
                    return '<span class="label label-sm label-success">'.DateFormat($Invoice->date_expired,'d/m/Y H:i:s').'</span>';
                }else{
                    return '<span class="label label-sm label-danger">'.DateFormat($Invoice->date_expired,'d/m/Y H:i:s').'</span>';
                }
            })

            ->editColumn('total', function ($Invoice) {
                return "<i>Rp ".number_format($Invoice->total,0,",",".")."</i>";
            })

            ->editColumn('paid', function ($Invoice) {
                if($Invoice->paid == 'Yes'){
                    return '<span class="label label-sm label-success">'.$Invoice->paid.'</span>';
                }else{
                    return '<span class="label label-sm label-danger">'.$Invoice->paid.'</span>';
                }
            })

            ->rawColumns(['href','total','date_expired','paid'])
            ->make(true);
    }


    public function listInvoice(){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Daftar Invoice';

        return Theme::view('modules.invoice.invoice.list',$this->_data);
    }


    public function showInvoice($InvoiceID){
        $Invoice                                    = InvoiceModel::find($InvoiceID);
        $this->_data['Invoice']                     = $Invoice;
        $this->_data['Setting']                     = SettingModel::find(1);


        $ArrDetail                                  = array();
        foreach($Invoice->invoice_detail as $detail){
            array_push($ArrDetail,$detail->total);
        }

        $this->_data['SubTotal']                    = array_sum($ArrDetail);

//        dd($Invoice->invoice_detail);

        return Theme::view('modules.invoice.invoice.show',$this->_data);
    }
}
