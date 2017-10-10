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
        $this->_data['DateNow']                     = date('d-m-Y');

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

    public function getInvoice(Request $request){
        $InvoiceID                                  = $request->id;
        $Invoice                                    = InvoiceModel::find($InvoiceID);
        if($Invoice){
            $data                                       = array(
                "status"                                    => true,
                "output"                                    => array(
                    "invoice_id"                            => $Invoice->id,
                    "website_id"                            => $Invoice->website_id,
                    "noinvoice"                             => $Invoice->invoice_number,
                    "domain_info"                           => $Invoice->domain_info,
                    "domain"                                => $Invoice->domain,
                    "date_transaction_display"              => DateFormat($Invoice->date_transaction,"d-m-Y"),
                    "date_transaction"                      => DateFormat($Invoice->date_transaction,"Y-m-d H:i:s"),
                    "date_expired_display"                  => DateFormat($Invoice->date_expired,"d-m-Y"),
                    "date_expired"                          => DateFormat($Invoice->date_expired,"Y-m-d H:i:s"),
                    "paid"                                  => $Invoice->paid,
                    "total_display"                         => number_format($Invoice->total,0,",","."),
                    "total"                                 => $Invoice->total,
                    "date_now"                              => date('d-m-Y')
                ),
                "message"                                   => 'OK'
            );
        }else{
            $data                                       = array(
                "status"                                    => false,
                "message"                                   => 'Data tidak ditemukan'
            );
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function saveInvoice(Request $request){

        $validator = Validator::make($request->all(), [
            'bank_information'                  => 'required',
            'name'                              => 'required',
            'account_of_bank'                   => 'required',
            'total'                             => 'required',
            'date_transaction'                  => 'required|date|date_format:d-m-Y'
        ],[
            'bank_information.required'         => 'Informasi Bank Wajib diisi!',
            'name.required'                     => 'Nama Pengirim wajib diisi!',
            'account_of_bank.required'          => 'No. rekening wajib diisi!',
            'date_transaction.date'             => 'Format Tanggal Bayar salah!',
            'date_transaction.date_format'      => 'Format Tanggal Bayar (Tanggal-Bulan-Tahun)'
        ]);

        if ($validator->fails()) {
            $data                                   = array(
                "status"                                => false,
                "message"                               => $validator->errors()->all(),
                "validator"                             => $validator->errors()
            );
        }else{
            $Invoice                                    = InvoiceModel::find($request->invoice_id);
            $Confirmation                               = new ConfirmationModel();
            $Confirmation->user_id                      = Auth::id();
            $Confirmation->website_id                   = $Invoice->website_id;
            $Confirmation->invoice_id                   = $request->invoice_id;
            $Confirmation->confirmation_type_id         = 1; # Website #
            $Confirmation->name                         = $request->name;
            $Confirmation->bank_information             = $request->bank_information;
            $Confirmation->account_of_bank              = $request->account_of_bank;
            $Confirmation->date_transaction             = DateFormat($request->date_transaction,"Y-m-d");
            $Confirmation->total                        = set_clearFormatRupiah($request->total);
            $Confirmation->created_by                   = Auth::id();
            $Confirmation->updated_by                   = Auth::id();

            try {
                $Confirmation->save();
                    $data                                    = array(
                        "status"                                => true,
                        "message"                               => 'Berhasil! Konfirmasi Pembayaran telah kami terima.',
                        "output"                                => array(
                            "id"                                => $request->invoice_id,
                            "domain"                            => $Invoice->domain
                        )
                    );
            }catch (\Exception $e) {
                $data                                    = array(
                    "status"                                => false,
                    "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                );
                $Details                                = array(
                    "user_id"                       => Auth::id(),
                    "website_id"                    => $Invoice->website_id,
                    "invoice_id"                    => $request->invoice_id,
                    "name"                          => $request->name,
                    "bank_information"              => $request->bank_information,
                    "account_of_bank"               => $request->account_of_bank,
                    "date_transaction"              => $request->date_transaction,
                    "total"                         => $request->total
                );
                Activity::log([
                    'contentId'     => $request->invoice_id,
                    'contentType'   => 'Confirmation Request',
                    'action'        => 'saveInvoice',
                    'description'   => "Ada kesalahan teknis pada form confirmation",
                    'details'       => $e->getMessage(),
                    'data'          => json_encode($Details),
                    'updated'       => Auth::id(),
                ]);
            }
        }
        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }


}
