<?php

namespace App\Modules\Template\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

use App\Modules\Template\Models\Template as TemplateModel;

use Theme;
use Auth;
use Activity;
use Debugbar;
use File;


class TemplateController extends Controller
{

    protected $_data                                = array();
    protected $PathTemplate                         = array();

    public function __construct()
    {
        $this->middleware('permission:master-menu-view');
        $this->urlTemplate                          = 'media/template/';
        $this->PathTemplate                         = public_path($this->urlTemplate);

        $this->_data['string_menuname']             = 'Template';
    }

    public function datatables(){
        $Template = TemplateModel::select(['id', 'name', 'price','preview_desktop as preview','is_active']);

        return Datatables::of($Template)
            ->addColumn('href', function ($Template) {
                return '<a href="javascript:void(0);" class="btn btn-info" onclick="editList('.$Template->id.')"><i class="glyphicon glyphicon-pencil"></i></a>
                        <button class="btn btn-warning" onclick="inactiveList('.$Template->id.')"><i class="fa fa-ban"></i></button>
                        <button class="btn btn-danger" onclick="deleteList('.$Template->id.')"><i class="fa fa-trash"></i></button>
                        ';
            })

            ->editColumn('price', function ($Template) {
                return number_format($Template->price,0,",",".");
            })

            ->editColumn('preview', function ($Template) {
                if($Template ->preview){
                   $Preview =  url('/')."/".$this->urlTemplate.$Template->preview;
                    return '<img src="'.$Preview.'" style="width:100px;">';
                }else{
                    return "";
                }
            })


            ->editColumn('is_active', function ($Template) {
                if($Template->is_active == 1){
                    return '<span class="label label-sm label-success">'.get_active($Template->is_active).'</span>';
                }else{
                    return '<span class="label label-sm label-danger">'.get_active($Template->is_active).'</span>';
                }
            })


            ->rawColumns(['href','preview','is_active'])
            ->make(true);
    }

    public function showTemplate(){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Template';

        return Theme::view('modules.template.admin.show',$this->_data);
    }

    public function showTemplateWithMsg($ScsMsg){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Template';
        $this->_data['ScsMsg']                      = base64_decode($ScsMsg);

        return Theme::view('modules.template.admin.show',$this->_data);
    }



    public function saveTemplate(Request $request){

        $validator = Validator::make($request->all(), [
            'name'                              => 'required',
            'price_up'                          => 'required',
            'price'                             => 'required'
        ],[
            'name.required'                     => 'Nama Wajib diisi!',
            'price_up.required'                 => 'Harga reseller wajib diisi!',
            'price.required'                    => 'Harga wajib diisi!'
        ]);

        if ($validator->fails()) {
            $data                                   = array(
                "status"                                => false,
                "message"                               => $validator->errors()->all(),
                "validator"                             => $validator->errors()
            );
        }else{
            if($request->statusform == 'edit'){
                $Template                            = TemplateModel::find($request->template_id);
            }else{
                $Template                            = new TemplateModel();
            }
            $Template->name                         = $request->name;
            $Template->demo_url                     = $request->demo_url;
            $Template->price_up                     = set_clearFormatRupiah($request->price_up);
            $Template->price                        = set_clearFormatRupiah($request->price);
            $Template->is_active                    = 1;
            $Template->created_by                   = Auth::id();
            $Template->updated_by                   = Auth::id();

            try {
                $Template->save();

               $flag                                                    = 0;

                ### UPLOAD DESIGN DESKTOP ###
                if($request->file('imageFileDesktop')){
                    $ImageDesktop                                       = $request->file('imageFileDesktop');
                    try {
                        $ImageFilesDesktop                                  = $this->imageUpload($ImageDesktop);

                        $TemplateInfo                                       = TemplateModel::find($Template->id);
                        $TemplateInfo->preview_desktop                      = $ImageFilesDesktop;
                        $TemplateInfo->save();
                    }
                    catch (\Exception $e) {
                        $flag                                            = 1;
                        $data                                    = array(
                            "status"                                => false,
                            "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                        );
                        Activity::log([
                            'contentId'   => $Template->id,
                            'contentType' => 'Website Template',
                            'action'      => 'saveTemplate',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($ImageFilesDesktop),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }
                ### END UPLOAD DESIGN DESKTOP ###

                ### UPLOAD DESIGN TABLET ###
                if($request->file('imageFileTablet')){
                    $ImageTablet                                        = $request->file('imageFileTablet');
                    try {
                        $ImageFilesTablet                                   = $this->imageUpload($ImageTablet);

                        $TemplateInfo                                       = TemplateModel::find($Template->id);
                        $TemplateInfo->preview_tablet                       = $ImageFilesTablet;
                        $TemplateInfo->save();
                    }
                    catch (\Exception $e) {
                        $flag                                               = 1;
                        $data                                    = array(
                            "status"                                => false,
                            "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                        );
                        Activity::log([
                            'contentId'   => $Template->id,
                            'contentType' => 'Template',
                            'action'      => 'saveTemplate',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($ImageFilesDesktop),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }
                ### END UPLOAD DESIGN TABLET ###

                ### UPLOAD DESIGN MOBILE ###
                if($request->file('imageFileMobile')){
                    $ImageMobile                                            = $request->file('imageFileMobile');
                    try {
                        $ImageFilesMobile                                   = $this->imageUpload($ImageMobile);

                        $TemplateInfo                                       = TemplateModel::find($Template->id);
                        $TemplateInfo->preview_mobile                       = $ImageFilesMobile;
                        $TemplateInfo->save();
                    }
                    catch (\Exception $e) {
                        $flag                                               = 1;
                        $data                                    = array(
                            "status"                                => false,
                            "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                        );
                        Activity::log([
                            'contentId'   => $Template->id,
                            'contentType' => 'Template',
                            'action'      => 'saveTemplate',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($ImageFilesDesktop),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }
                ### END UPLOAD DESIGN MOBILE ###

                if($flag == 0){
                    $TemplateInfo                               = TemplateModel::find($Template->id);
                    if($TemplateInfo){
                        $Preview                                = url('/')."/".$this->urlTemplate.$TemplateInfo->preview_desktop;
                    }else{
                        $Preview                                = "";
                    }
                    $data                                    = array(
                        "status"                                => true,
                        "message"                               => 'Berhasil! Moment terbaikmu telah tersimpan.',
                        "output"                                => array(
                            "id"                                => $Template->id,
                            "name"                              => $Template->name,
                            "price"                             => number_format($Template->price,0,",","."),
                            "preview"                           => $Preview,
                            "statusform"                        => $request->statusform
                        )
                    );
                }

            }catch (\Exception $e) {
                $data                                    = array(
                    "status"                                => false,
                    "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                );
                $Details                                = array(
                    "name"                      => $request->website_id,
                    "demo_url"                  => $request->title,
                    "price_up"                  => $request->description,
                    "price"                     => $request->date_story,
                    "priview_desktop"           => json_encode($request->file('imageFileDesktop')),
                    "priview_tablet"            => json_encode($request->file('imageFileTablet')),
                    "priview_mobile"            => json_encode($request->file('imageFileMobile')),
                );
                Activity::log([
                    'contentId'   => 0,
                    'contentType' => 'Template',
                    'action'      => 'saveTemplate',
                    'description' => $e->getMessage(),
                    'details'     => json_encode($Details),
                    'updated'     => Auth::id(),
                ]);
            }
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function getTemplate(Request $request){
        $TemplateID                                 = $request->id;
        $Template                                   = TemplateModel::find($TemplateID);
        if($Template){
            ## DESKTOP ###
            if($Template->preview_desktop){
                $imageFileDesktop                   = $Template->preview_desktop;
                $FileDesktop                        = url('/').$this->urlTemplate.$imageFileDesktop;
                $StatusFileDesktop                  = 1;
            }else{
                $imageFileDesktop                   = "";
                $FileDesktop                        = "";
                $StatusFileDesktop                  = 2;
            }
            ## DESKTOP ###

            ## TABLET ###
            if($Template->preview_tablet){
                $imageFileTablet                    = $Template->preview_tablet;
                $FileTablet                         = url('/').$this->urlTemplate.$imageFileTablet;
                $StatusFileTablet                   = 1;
            }else{
                $imageFileTablet                    = "";
                $FileTablet                         = "";
                $StatusFileTablet                   = 2;
            }
            ## TABLET ###

            ## MOBILE ###
            if($Template->preview_mobile){
                $imageFileMobile                    = $Template->preview_mobile;
                $FileMobile                         = url('/').$this->urlTemplate.$imageFileMobile;
                $StatusFileMobile                   = 1;
            }else{
                $imageFileMobile                    = "";
                $FileMobile                         = "";
                $StatusFileMobile                   = 2;
            }
            ## MOBILE ###
            $data                                       = array(
                "status"                                    => true,
                "output"                                    => array(
                    "template_id"                           => $TemplateID,
                    "name"                                  => $Template->name,
                    "demo_url"                              => $Template->demo_url,
                    "price_up"                              => $Template->price_up,
                    "price"                                 => $Template->price,
                    "imagefileDesktop"                      => $imageFileDesktop,
                    "fileDesktop"                           => $FileDesktop,
                    "statusfileDesktop"                     => $StatusFileDesktop,
                    "imagefileTablet"                       => $imageFileTablet,
                    "fileTablet"                            => $FileTablet,
                    "statusfileTablet"                      => $StatusFileTablet,
                    "imagefileMobile"                       => $imageFileMobile,
                    "fileMobile"                            => $FileMobile,
                    "statusfileMobile"                      => $StatusFileMobile
                ),
                "message"                                   => 'Data anda telah ditampilkan'
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


    public function deleteImageTemplateDesktop(Request $request){
        $TemplateID                                     = $request->id;
        $Template                                       = TemplateModel::find($TemplateID);

        if($Template){
            if($Template->preview_desktop){
                $File                                           = $this->urlTemplate.$Template->preview_desktop;
                $ExistFile                                      = File::exists($File);
                if($ExistFile){
                    try {
                        $DeleteFile                             = File::delete($File);
                        $Template->preview_desktop              = "";
                        $Template->save();
                        $data                                       = array(
                            "status"                                    => true,
                            "request"                                   => $TemplateID,
                            "file"                                      => $ExistFile,
                            "message"                                   => 'Foto Preview Desktop kamu berhasil dihapus'
                        );
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $TemplateID,
                            "file_locate"                               => $File,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $File,
                            'template_id'                          => $TemplateID
                        );

                        Activity::log([
                            'contentId'   => $TemplateID,
                            'contentType' => 'Template',
                            'action'      => 'deleteImageTemplateDesktop',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $TemplateID,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $TemplateID,
                        'contentType' => 'Template',
                        'action'      => 'deleteImageTemplateDesktop',
                        'description' => 'File Not Found',
                        'details'     => json_encode($File),
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                $data                                       = array(
                    "status"                                    => false,
                    "request"                                   => $TemplateID,
                    "message"                                   => 'Data kosong'
                );

                Activity::log([
                    'contentId'   => $TemplateID,
                    'contentType' => 'Template',
                    'action'      => 'deleteImageTemplateDesktop',
                    'description' => "Data kosong",
                    'details'     => json_encode($data),
                    'updated'     => Auth::id(),
                ]);
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $TemplateID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'template_id'                          => $TemplateID
            );

            Activity::log([
                'contentId'   => $TemplateID,
                'contentType' => 'Template',
                'action'      => 'deleteImageTemplateDesktop',
                'description' => "Data Tidak ditemukan di database.",
                'details'     => json_encode($Details),
                'updated'     => Auth::id(),
            ]);
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }


    public function deleteImageTemplateTablet(Request $request){
        $TemplateID                                     = $request->id;
        $Template                                       = TemplateModel::find($TemplateID);

        if($Template){
            if($Template->preview_tablet){
                $File                                           = $this->urlTemplate.$Template->preview_tablet;
                $ExistFile                                      = File::exists($File);
                if($ExistFile){
                    try {
                        $DeleteFile                             = File::delete($File);
                        $Template->preview_tablet              = "";
                        $Template->save();
                        $data                                       = array(
                            "status"                                    => true,
                            "request"                                   => $TemplateID,
                            "file"                                      => $ExistFile,
                            "message"                                   => 'Foto Preview Desktop kamu berhasil dihapus'
                        );
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $TemplateID,
                            "file_locate"                               => $File,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $File,
                            'template_id'                               => $TemplateID
                        );

                        Activity::log([
                            'contentId'   => $TemplateID,
                            'contentType' => 'Template',
                            'action'      => 'deleteImageTemplateTablet',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $TemplateID,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $TemplateID,
                        'contentType' => 'Template',
                        'action'      => 'deleteImageTemplateTablet',
                        'description' => 'File Not Found',
                        'details'     => json_encode($File),
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                $data                                       = array(
                    "status"                                    => false,
                    "request"                                   => $TemplateID,
                    "message"                                   => 'Data kosong'
                );

                Activity::log([
                    'contentId'   => $TemplateID,
                    'contentType' => 'Template',
                    'action'      => 'deleteImageTemplateTablet',
                    'description' => "Data kosong",
                    'details'     => json_encode($data),
                    'updated'     => Auth::id(),
                ]);
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $TemplateID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'template_id'                          => $TemplateID
            );

            Activity::log([
                'contentId'   => $TemplateID,
                'contentType' => 'Template',
                'action'      => 'deleteImageTemplateTablet',
                'description' => "Data Tidak ditemukan di database.",
                'details'     => json_encode($Details),
                'updated'     => Auth::id(),
            ]);
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function deleteImageTemplateMobile(Request $request){
        $TemplateID                                     = $request->id;
        $Template                                       = TemplateModel::find($TemplateID);

        if($Template){
            if($Template->preview_mobile){
                $File                                           = $this->urlTemplate.$Template->preview_mobile;
                $ExistFile                                      = File::exists($File);
                if($ExistFile){
                    try {
                        $DeleteFile                             = File::delete($File);
                        $Template->preview_mobile              = "";
                        $Template->save();
                        $data                                       = array(
                            "status"                                    => true,
                            "request"                                   => $TemplateID,
                            "file"                                      => $ExistFile,
                            "message"                                   => 'Foto Preview Desktop kamu berhasil dihapus'
                        );
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $TemplateID,
                            "file_locate"                               => $File,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $File,
                            'template_id'                               => $TemplateID
                        );

                        Activity::log([
                            'contentId'   => $TemplateID,
                            'contentType' => 'Template',
                            'action'      => 'deleteImageTemplateMobile',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $TemplateID,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $TemplateID,
                        'contentType' => 'Template',
                        'action'      => 'deleteImageTemplateMobile',
                        'description' => 'File Not Found',
                        'details'     => json_encode($File),
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                $data                                       = array(
                    "status"                                    => false,
                    "request"                                   => $TemplateID,
                    "message"                                   => 'Data kosong'
                );

                Activity::log([
                    'contentId'   => $TemplateID,
                    'contentType' => 'Template',
                    'action'      => 'deleteImageTemplateMobile',
                    'description' => "Data kosong",
                    'details'     => json_encode($data),
                    'updated'     => Auth::id(),
                ]);
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $TemplateID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'template_id'                          => $TemplateID
            );

            Activity::log([
                'contentId'   => $TemplateID,
                'contentType' => 'Template',
                'action'      => 'deleteImageTemplateMobile',
                'description' => "Data Tidak ditemukan di database.",
                'details'     => json_encode($Details),
                'updated'     => Auth::id(),
            ]);
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }


    public function deleteTemplate(Request $request)
    {
        $TemplateID                                     = $request->id;
        $Template                                       = TemplateModel::find($TemplateID);
        if($Template){
            ### DESKTOP ###
            if($Template->preview_desktop){
                $FileDesktop                                    = $this->urlTemplate.$Template->preview_desktop;
                $ExistFileDesktop                               = File::exists($FileDesktop);
                if($ExistFileDesktop){
                    try {
                        $DeleteFileDesktop                             = File::delete($FileDesktop);
                        $Template->delete();
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $TemplateID,
                            "file_locate"                               => $FileDesktop,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $FileDesktop,
                            'template_id'                          => $TemplateID
                        );

                        Activity::log([
                            'contentId'   => $TemplateID,
                            'contentType' => 'Template',
                            'action'      => 'deleteTemplate',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $TemplateID,
                        "file_locate"                               => $FileDesktop,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $TemplateID,
                        'contentType' => 'Template',
                        'action'      => 'deleteTemplate',
                        'description' => 'File Not Found',
                        'details'     => json_encode($FileDesktop),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
            ### DESKTOP ###

            ### TABLET ###
            if($Template->preview_tablet){
                $FileTablet                                     = $this->urlTemplate.$Template->preview_tablet;
                $ExistFileTablet                                = File::exists($FileTablet);
                if($ExistFileTablet){
                    try {
                        $DeleteFileTablet                           = File::delete($FileTablet);
                        $Template->delete();
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $TemplateID,
                            "file_locate"                               => $FileTablet,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $FileTablet,
                            'template_id'                               => $TemplateID
                        );

                        Activity::log([
                            'contentId'   => $TemplateID,
                            'contentType' => 'Template',
                            'action'      => 'deleteTemplate',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $TemplateID,
                        "file_locate"                               => $FileTablet,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $TemplateID,
                        'contentType' => 'Template',
                        'action'      => 'deleteTemplate',
                        'description' => 'File Not Found',
                        'details'     => json_encode($FileTablet),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
            ### TABLET ###


            ### MOBILE ###
            if($Template->preview_mobile){
                $FileMobile                                     = $this->urlTemplate.$Template->preview_mobile;
                $ExistFileMobile                                = File::exists($FileMobile);
                if($ExistFileMobile){
                    try {
                        $DeleteFileMobile                           = File::delete($FileMobile);
                        $Template->delete();
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $TemplateID,
                            "file_locate"                               => $FileMobile,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $FileMobile,
                            'template_id'                               => $TemplateID
                        );

                        Activity::log([
                            'contentId'   => $TemplateID,
                            'contentType' => 'Template',
                            'action'      => 'deleteTemplate',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $TemplateID,
                        "file_locate"                               => $FileMobile,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $TemplateID,
                        'contentType' => 'Template',
                        'action'      => 'deleteTemplate',
                        'description' => 'File Not Found',
                        'details'     => json_encode($FileMobile),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
            ### MOBILE ###


            try {
                $Template->delete();
                $data                                       = array(
                    "status"                                    => true,
                    "request"                                   => $TemplateID,
                    "message"                                   => 'Template berhasil dihapus'
                );
            }catch (\Exception $e) {
                $data                                       = array(
                    "status"                                    => false,
                    "request"                                   => $TemplateID,
                    "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                );

                $Details                                    = array(
                    'template_id'                               => $TemplateID
                );

                Activity::log([
                    'contentId'   => $TemplateID,
                    'contentType' => 'Template',
                    'action'      => 'deleteTemplate',
                    'description' => $e->getMessage(),
                    'details'     => json_encode($Details),
                    'updated'     => Auth::id(),
                ]);
            }

        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $TemplateID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'template_id'                          => $TemplateID
            );

            Activity::log([
                'contentId'   => $TemplateID,
                'contentType' => 'Template',
                'action'      => 'deleteTemplate',
                'description' => "Data Tidak ditemukan di database.",
                'details'     => json_encode($Details),
                'updated'     => Auth::id(),
            ]);
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Processing Save Image File.
     * @param \Illuminate\Http\UploadedFile Get Uploaded file for save it on server folder
     * @return String saved FileName
     */
    public function imageUpload($file_image){
        if ($file_image->isValid()) {
            $str_fileName = time().'-'.$file_image->getClientOriginalName();
            $file_image->move($this->PathTemplate, $str_fileName);
            return $str_fileName;
        }
    }

}
