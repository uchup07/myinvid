<?php

namespace App\Modules\Gallery\Http\Controllers;

use App\Modules\Gallery\Models\Gallery;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Gallery\Models\Gallery as GalleryModel;


use Theme;
use Auth;
use Activity;
use Debugbar;
use File;


class GalleryController extends Controller
{
    protected $_data = array();
    protected $PathGallery = array();

    public function __construct()
    {
        $this->middleware('permission:website-menu');
        $this->urlPath                                  = 'media/gallery/';
        $this->PathGallery                              = public_path($this->urlPath);

        $this->_data['string_menuname'] = 'Website';
    }

    public function formGallery($WebsiteID)
    {
        $this->_data['state']                           = 'add';
        $this->_data['string_active_menu'] = 'Create New';

        $Users = Auth::user();
        $CountGallery                                   = GalleryModel::where('user_id','=',Auth::id())->where('website_id','=',$WebsiteID)->count();
        $ImageGallery                                   = GalleryModel::where('user_id','=',Auth::id())->where('website_id','=',$WebsiteID)->get();
        $this->_data['User']                            = $Users;
        $this->_data['website_id']                      = $WebsiteID;
        $this->_data['WebsiteInfo']                     = WebsiteModel::find($WebsiteID);
        $this->_data['count_gallery']                   = $CountGallery +1;
        $this->_data['ImageGallery']                    = $ImageGallery;


        return Theme::view('modules.gallery.wedding_gallery.form', $this->_data);
    }

    public function saveGallery(Request $request)
    {
        $WebsiteID                                      = $request->website_id;
        $File                                           = $request->file('imageFile');
        $x                                              = count($request->imageFile);
        $ErrMsg                                         = "";
        if($x > 0){
            for($i=0;$i<$x;$i++){
                try {
                    $ImageFiles                             = $this->imageUpload($File[$i]);
                    $Gallery                                = new Gallery();
                    $Gallery->user_id                       = Auth::id();
                    $Gallery->website_id                    = $WebsiteID;
                    $Gallery->file                          = $ImageFiles;
                    $Gallery->created_by                    = Auth::id();
                    $Gallery->updated_by                    = Auth::id();
                    try {
                        $Gallery->save();
                    }catch (\Exception $e) {
                        $ErrMsg                                 = "Maaf, ada kesalahan teknis. Silakan hubungi Web administrator [barindra1988@gmail.com]";
                        $this->_data['ErrMsg']                  = $ErrMsg;
                        $Data                                   = array(
                            "user_id"               => Auth::id(),
                            "website_id"            => $WebsiteID,
                            "file"                  => $ImageFiles
                        );
                        Activity::log([
                            'contentId'   => $WebsiteID,
                            'contentType' => 'Website Gallery',
                            'action'      => 'saveGallery',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Data),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }catch (\Exception $e) {
                    $ErrMsg                                 = "Maaf, ada kesalahan teknis. Silakan hubungi Web administrator [barindra1988@gmail.com]";
                    $this->_data['ErrMsg']                  = $ErrMsg;
                    Activity::log([
                        'contentId'   => $WebsiteID,
                        'contentType' => 'Website Gallery',
                        'action'      => 'saveGallery',
                        'description' => $e->getMessage(),
                        'details'     => json_encode($File[$i]->getClientOriginalName()),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
            if($ErrMsg){
                return redirect()
                    ->route('wedding_gallery_form',$WebsiteID)
                    ->with('ErrMsg',$ErrMsg);
            }else{
                return redirect()
                    ->route('wedding_gallery_form',$WebsiteID)
                    ->with('ScsMsg',"Foto anda berhasil diunngah.");
            }
        }else{
            return redirect()
                ->route('wedding_gallery_form',$WebsiteID)
                ->with('WrngMsg',"Mohon Masukan Foto anda bersama pasangan anda");
        }

    }

    public function uploadGallery(Request $request)
    {
        $GalleryInfo                                    = GalleryModel::find($request->gallery_id);
        if($GalleryInfo){
            $File                                       = $this->urlPath.$GalleryInfo->file;
            $ExistFile                                  = File::exists($File);
            $WebsiteID                                  = $GalleryInfo->website_id;
            if($ExistFile){
                try {
                    $DeleteFile                             = File::delete($File);
                    $GalleryInfo->delete();
                    $data                                       = array(
                        "status"                                    => true,
                        "request"                                   => $request->gallery_id,
                        "file"                                      => $ExistFile,
                        'website_id'                                => $WebsiteID,
                        "message"                                   => 'Kamu berhasil menghapus file'
                    );
                }catch (\Exception $e) {
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $request->gallery_id,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                    );

                    $Details                            = array(
                        'file_locate'                               => $File,
                        'request'                                   => $request->gallery_id
                    );

                    Activity::log([
                        'contentId'   => $request->gallery_id,
                        'contentType' => 'Website Gallery',
                        'action'      => 'uploadGallery',
                        'description' => $e->getMessage(),
                        'details'     => $Details,
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                $data                                       = array(
                    "status"                                    => false,
                    "request"                                   => $request->gallery_id,
                    "file_locate"                               => $File,
                    "message"                                   => 'Maaf, Data tidak ditemukan.'
                );
                Activity::log([
                    'contentId'   => $request->gallery_id,
                    'contentType' => 'Website Gallery',
                    'action'      => 'uploadGallery',
                    'description' => 'File Not Found',
                    'details'     => json_encode($File),
                    'updated'     => Auth::id(),
                ]);
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $request->gallery_id,
                "message"                                   => 'Maaf, Data tidak ditemukan.'
            );
            Activity::log([
                'contentId'   => $request->gallery_id,
                'contentType' => 'Website Gallery',
                'action'      => 'uploadGallery',
                'description' => 'Data Not Found',
                'details'     => json_encode($data),
                'updated'     => Auth::id(),
            ]);
        }


        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function loadGallery($WebsiteID)
    {
        $Users                                          = Auth::user();
        $ImageGallery                                   = GalleryModel::where('user_id','=',Auth::id())->where('website_id','=',$WebsiteID)->get();

        $this->_data['User']                            = $Users;
        $this->_data['website_id']                      = $WebsiteID;
        $this->_data['WebsiteInfo']                     = WebsiteModel::find($WebsiteID);
        $this->_data['ImageGallery']                    = $ImageGallery;

        return Theme::view('modules.gallery.wedding_gallery.load_gallery', $this->_data);
    }


    /**
     * Processing Save Image File.
     * @param \Illuminate\Http\UploadedFile Get Uploaded file for save it on server folder
     * @return String saved FileName
     */
    public function imageUpload($file_image){
        if ($file_image->isValid()) {
            $str_fileName = time().'-'.$file_image->getClientOriginalName();
            $file_image->move($this->PathGallery, $str_fileName);

            return $str_fileName;
        }
    }
}