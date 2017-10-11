<?php

namespace App\Modules\Email\Http\Controllers;

use App\Modules\Email\Models\EmailFormat;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

use App\Modules\Email\Models\EmailFormat as EmailFormatModel;
use App\Modules\Website\Models\Website as WebsiteModel;

use Theme;
use Auth;
use Activity;
use Debugbar;


class EmailFormatController extends Controller
{
    protected $_data                                = array();
    protected $PathEmailFormat                      = array();

    public function __construct()
    {
        $this->middleware('permission:email-menu');
        $this->middleware('permission:email-format-view');
        $this->_data['string_menuname']             = 'Undangan Email';

        $this->urlEmailFormat                       = 'media/email/format/';
        $this->PathEmailFormat                      = public_path($this->urlEmailFormat);
    }


    public function datatables(){
        $UserID         = Auth::id();
        $Website        = WebsiteModel::select(['id','domain','is_active'])
                            ->where('user_id','=',$UserID)
                            ->where('is_active','=',1);

        return Datatables::of($Website)
            ->addColumn('href', function ($Website) {
                return '
                        <button class="btn btn-info btn-circle" onclick="setFormat('.$Website->id.')"><i class="fa fa-list"></i></button>
                        ';
            })

            ->editColumn('is_active', function ($Website) {
                if($Website->is_active == 1){ # is_active #
                    return '<span class="label label-sm label-success">active</span>';
                }else{
                    return '<span class="label label-sm label-danger">inactive</span>';
                }
            })

            ->rawColumns(['href','is_active'])
            ->make(true);
    }

    public function showList(){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Daftar Website';
        $this->_data['DateNow']                     = date('d-m-Y');

        return Theme::view('modules.email.format.list',$this->_data);
    }

    public function getFormat(Request $request){
        $WebsiteID                                      = $request->id;

        $Website                                        = WebsiteModel::find($WebsiteID);
        if($Website){
            if(EmailFormatModel::where('website_id','=',$WebsiteID)->count() > 0){
                $EmailFormat                            = EmailFormatModel::where('website_id','=',$WebsiteID)->first();

                if($EmailFormat->attachment){
                    $attachmentFile                     = $EmailFormat->attachment;
                    $Attachment                         = url('/').$this->urlEmailFormat.$EmailFormat->attachment;
                    $StatusAttachment                   = 1;
                }else{
                    $attachmentFile                          = "";
                    $Attachment                         = "";
                    $StatusAttachment                         = 2;
                }

                $data                                       = array(
                    "status"                                    => true,
                    "flag"                                      => 1,
                    "output"                                    => array(
                        "id"                                    => $EmailFormat->id,
                        "title"                                 => $EmailFormat->title,
                        "content"                               => $EmailFormat->content,
                        "attachment"                            => $EmailFormat->attachment,
                        "website_id"                            => $WebsiteID,
                        "attachmentfile"                        => $attachmentFile,
                        "attachment"                            => $Attachment,
                        "statusattachment"                      => $StatusAttachment

                    ),
                    "message"                                   => 'OK'
                );
            }else{
                $data                                       = array(
                    "status"                                    => true,
                    "flag"                                      => 2,
                    "message"                                   => 'Format Baru'
                );
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "message"                                   => 'Data tidak ditemukan'
            );
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }
}
