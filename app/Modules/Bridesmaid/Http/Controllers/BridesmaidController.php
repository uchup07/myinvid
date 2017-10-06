<?php

namespace App\Modules\Bridesmaid\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;


use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Bridesmaid\Models\Bridesmaid as BridesmaidModel;

use Theme;
use Auth;
use Activity;
use Debugbar;
use File;


class BridesmaidController extends Controller
{

    protected $_data                                = array();
    protected $PathBridesmaid                       = array();

    public function __construct()
    {
        $this->middleware('permission:website-menu');
        $this->urlBridesmaid                        = 'media/bridesmaid/';
        $this->PathBridesmaid                       = public_path($this->urlBridesmaid);

        $this->_data['string_menuname']             = 'Website';
    }

    public function datatables($WebsiteID){
        $Bridesmaid = BridesmaidModel::select(['id', 'gender', 'name','file'])
            ->where('website_id','=',$WebsiteID);

        return Datatables::of($Bridesmaid)
            ->addColumn('href', function ($Bridesmaid) {
                return '<a href="javascript:void(0);" class="btn btn-info" onclick="editList('.$Bridesmaid->id.')"><i class="glyphicon glyphicon-pencil"></i></a>
                        <button class="btn btn-danger" onclick="deleteList('.$Bridesmaid->id.')"><i class="fa fa-ban"></i></button>
                        ';
            })

            ->editColumn('file', function ($Bridesmaid) {
                return '<img src="'.url('/')."/".get_GenerateLinkImage($this->urlBridesmaid,$Bridesmaid->file).'" style="width:100px;">';
            })

            ->rawColumns(['href','file'])
            ->make(true);
    }


    public function showBridesmaid($WebsiteID){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Bridesmaid';

        $Users                                      = Auth::user();
        $this->_data['User']                        = $Users;
        $this->_data['WebsiteID']                   = $WebsiteID;


        return Theme::view('modules.bridesmaid.bridesmaid.list',$this->_data);
    }

    public function showBridesmaidWithMsg($WebsiteID,$ScsMsg)
    {
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Bridesmaid';

        $Users                                      = Auth::user();
        $this->_data['User']                        = $Users;
        $this->_data['WebsiteID']                   = $WebsiteID;
        $this->_data['ScsMsg']                      = base64_decode($ScsMsg);

        return Theme::view('modules.bridesmaid.bridesmaid.list',$this->_data);
    }

    public function saveBridesmaid(Request $request){

        $validator = Validator::make($request->all(), [
            'name'                              => 'required',
            'gender'                            => 'required'
        ],[
            'name.required'                     => 'Nama Wajib diisi!',
            'gender.required'                   => 'Jenis Kelamin wajib diisi!'
        ]);

        if ($validator->fails()) {
            $data                                       = array(
                "status"                                => false,
                "message"                               => $validator->errors()->all(),
                "validator"                             => $validator->errors()
            );
        }else{
            if($request->statusform == 'edit'){
                $Bridesmaid                             = BridesmaidModel::find($request->bridesmaid_id);
            }else{
                $Bridesmaid                             = new BridesmaidModel();
            }
            $Bridesmaid->user_id                        = Auth::id();
            $Bridesmaid->website_id                     = $request->website_id;
            $Bridesmaid->name                           = $request->name;
            $Bridesmaid->gender                         = $request->gender;
            $Bridesmaid->created_by                     = Auth::id();
            $Bridesmaid->updated_by                     = Auth::id();

            try {
                $Bridesmaid->save();

                ### UPLOAD FOTO ###
                if($request->file('imageFile')){
                    $Image                                      = $request->file('imageFile');
                    $ImageFiles                                 = $this->imageUpload($Image);

                    $BridesmaidInfo                             = BridesmaidModel::find($Bridesmaid->id);
                    $BridesmaidInfo->file                       = $ImageFiles;
                    try {
                        $BridesmaidInfo->save();
                        $data                                    = array(
                            "status"                                => true,
                            "message"                               => 'Berhasil! Informasi Sahabatmu telah tersimpan.',
                            "output"                                => array(
                                "id"                                => $BridesmaidInfo->id,
                                "name"                              => $BridesmaidInfo->name,
                                "gender"                            => $BridesmaidInfo->gender,
                                "file"                              => url('/')."/".$this->urlBridesmaid.$BridesmaidInfo->file,
                                "statusform"                        => $request->statusform
                            )
                        );
                    }
                    catch (\Exception $e) {
                        $data                                    = array(
                            "status"                                => false,
                            "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                        );
                        Activity::log([
                            'contentId'   => $Bridesmaid->id,
                            'contentType' => 'Website Bridesmaid',
                            'action'      => 'saveBridesmaid',
                            'description' => $e->getMessage(),
                            'details'     => "ada kesalahan pada saat penyimpanan data :". json_encode($ImageFiles),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }
                ### END UPLOAD FOTO ###
                else{
                    $data                                    = array(
                        "status"                                => true,
                        "message"                               => 'Berhasil! Informasi Sahabatmu telah tersimpan.',
                        "output"                                => array(
                            "id"                            => $Bridesmaid->id,
                            "name"                          => $Bridesmaid->name,
                            "gender"                        => $Bridesmaid->gender,
                            "statusform"                    => $request->statusform
                        )
                    );
                }

            }catch (\Exception $e) {
                $data                                    = array(
                    "status"                                => false,
                    "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                );
                $Details                                = array(
                    "user_id"               => Auth::id(),
                    "website_id"            => $request->website_id,
                    "name"                  => $request->name,
                    "gender"                => $request->gender
                );
                Activity::log([
                    'contentId'   => 0,
                    'contentType' => 'Website Bridesmaid',
                    'action'      => 'saveBridesmaid',
                    'description' => $e->getMessage(),
                    'details'     => json_encode($Details),
                    'updated'     => Auth::id(),
                ]);
            }
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function getBridesmaid(Request $request){
        $BridesmaidID                              = $request->id;
        $Bridesmaid                                = BridesmaidModel::find($BridesmaidID);
        if($Bridesmaid){
            if($Bridesmaid->file){
                $imageFile                          = $Bridesmaid->file;
                $File                               = url('/').$this->urlBridesmaid.$Bridesmaid->file;
                $StatusFile                         = 1;
            }else{
                $imageFile                          = "";
                $File                               = "";
                $StatusFile                         = 2;
            }
            $data                                       = array(
                "status"                                    => true,
                "output"                                    => array(
                    "bridesmaid_id"                         => $Bridesmaid->id,
                    "name"                                  => $Bridesmaid->name,
                    "gender"                                => $Bridesmaid->gender,
                    "imagefile"                             => $imageFile,
                    "file"                                  => $File,
                    "statusfile"                            => $StatusFile
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


    public function deleteImageBridesmaid(Request $request){
        $BridesmaidID                                   = $request->id;
        $Bridesmaid                                     = BridesmaidModel::find($BridesmaidID);

        if($Bridesmaid){
            $WebsiteID                                      = $Bridesmaid->website_id;
            if($Bridesmaid->file){
                $File                                           = $this->urlBridesmaid.$Bridesmaid->file;
                $ExistFile                                      = File::exists($File);
                if($ExistFile){
                    try {
                        $DeleteFile                             = File::delete($File);
                        $Bridesmaid->file                      = "";
                        $Bridesmaid->save();
                        $data                                       = array(
                            "status"                                    => true,
                            "request"                                   => $BridesmaidID,
                            "file"                                      => $ExistFile,
                            'website_id'                                => $WebsiteID,
                            "message"                                   => 'Foto moment kamu berhasil dihapus'
                        );
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $BridesmaidID,
                            "file_locate"                               => $File,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $File,
                            'story_of_love_id'                          => $BridesmaidID
                        );

                        Activity::log([
                            'contentId'   => $BridesmaidID,
                            'contentType' => 'Website Bridesmaid',
                            'action'      => 'deleteImageBridesmaid',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $BridesmaidID,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $BridesmaidID,
                        'contentType' => 'Website Bridesmaid',
                        'action'      => 'deleteImageBridesmaid',
                        'description' => 'File Not Found',
                        'details'     => json_encode($File),
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                try {
                    $Bridesmaid->delete();
                    $data                                       = array(
                        "status"                                    => true,
                        "request"                                   => $BridesmaidID,
                        'website_id'                                => $WebsiteID,
                        "message"                                   => 'Foto Sahabatmu berhasil dihapus'
                    );
                }catch (\Exception $e) {
                    $data                                       = array(
                        "status"                                    => false,
                        'website_id'                                => $WebsiteID,
                        "request"                                   => $BridesmaidID,
                        "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                    );

                    $Details                                = array(
                        'website_id'                                => $WebsiteID,
                        'bridesmaid_id'                             => $BridesmaidID
                    );

                    Activity::log([
                        'contentId'   => $BridesmaidID,
                        'contentType' => 'Website Bridesmaid',
                        'action'      => 'deleteImageBridesmaid',
                        'description' => $e->getMessage(),
                        'details'     => json_encode($Details),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $BridesmaidID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'bridesmaid_id'                          => $BridesmaidID
            );

            Activity::log([
                'contentId'   => $BridesmaidID,
                'contentType' => 'Website Bridesmaid',
                'action'      => 'deleteImageBridesmaid',
                'description' => "Data Tidak ditemukan di database.",
                'details'     => json_encode($Details),
                'updated'     => Auth::id(),
            ]);

        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function deleteBridesmaid(Request $request)
    {
        $BridesmaidID                                   = $request->id;
        $Bridesmaid                                     = BridesmaidModel::find($BridesmaidID);
        if($Bridesmaid){
            $WebsiteID                                      = $Bridesmaid->website_id;
            if($Bridesmaid->file){
                $File                                           = $this->urlBridesmaid.$Bridesmaid->file;
                $ExistFile                                      = File::exists($File);
                if($ExistFile){
                    try {
                        $DeleteFile                             = File::delete($File);
                        $Bridesmaid->delete();
                        $data                                       = array(
                            "status"                                    => true,
                            "request"                                   => $BridesmaidID,
                            "file"                                      => $ExistFile,
                            'website_id'                                => $WebsiteID,
                            "message"                                   => 'Foto Sahabatmu berhasil dihapus'
                        );
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $BridesmaidID,
                            "file_locate"                               => $File,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $File,
                            'bridesmaid_id'                             => $BridesmaidID
                        );

                        Activity::log([
                            'contentId'   => $BridesmaidID,
                            'contentType' => 'Website Bridesmaid',
                            'action'      => 'deleteBridesmaid',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $BridesmaidID,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $BridesmaidID,
                        'contentType' => 'Website Bridesmaid',
                        'action'      => 'deleteBridesmaid',
                        'description' => 'File Not Found',
                        'details'     => json_encode($File),
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                try {
                    $Bridesmaid->delete();
                    $data                                       = array(
                        "status"                                    => true,
                        "request"                                   => $BridesmaidID,
                        'website_id'                                => $WebsiteID,
                        "message"                                   => 'Foto Sahabatmu berhasil dihapus'
                    );
                }catch (\Exception $e) {
                    $data                                       = array(
                        "status"                                    => false,
                        'website_id'                                => $WebsiteID,
                        "request"                                   => $BridesmaidID,
                        "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                    );

                    $Details                                = array(
                        'website_id'                                => $WebsiteID,
                        'story_of_love_id'                          => $BridesmaidID
                    );

                    Activity::log([
                        'contentId'   => $BridesmaidID,
                        'contentType' => 'Website Bridesmaid',
                        'action'      => 'deleteBridesmaid',
                        'description' => $e->getMessage(),
                        'details'     => json_encode($Details),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $BridesmaidID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'bridesmaid_id'                          => $BridesmaidID
            );

            Activity::log([
                'contentId'   => $BridesmaidID,
                'contentType' => 'Website Bridesmaid',
                'action'      => 'deleteBridesmaid',
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
            $file_image->move($this->PathBridesmaid, $str_fileName);
            return $str_fileName;
        }
    }
}
