<?php

namespace App\Modules\Storyoflove\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Storyoflove\Models\StoryOfLove as StoryOfLoveModel;

use Theme;
use Auth;
use Activity;
use Debugbar;
use File;


class StoryOfLoveController extends Controller
{
    protected $_data                                = array();
    protected $PathStoryofLove                      = array();

    public function __construct()
    {
        $this->middleware('permission:website-menu');
        $this->urlStoryOfLove                       = 'media/storyoflove/';
        $this->PathStoryOfLove                      = public_path($this->urlStoryOfLove);

        $this->_data['string_menuname']             = 'Website';
    }

    public function datatables($WebsiteID){
        $StoryOfLove = StoryOfLoveModel::select(['id', 'title', 'description','date_story'])
                                        ->where('website_id','=',$WebsiteID);

        return Datatables::of($StoryOfLove)
            ->addColumn('href', function ($StoryOfLove) {
                return '<a href="javascript:void(0);" class="btn btn-info" onclick="editList('.$StoryOfLove->id.')"><i class="glyphicon glyphicon-pencil"></i></a>
                        <button class="btn btn-danger" onclick="deleteList('.$StoryOfLove->id.')"><i class="fa fa-ban"></i></button>
                        ';
            })

            ->editColumn('date_story', function ($StoryOfLove) {
                    return DateFormat($StoryOfLove->date_story,'d F Y');
            })

            ->rawColumns(['href'])
            ->make(true);
    }

    public function showStoryOfLove($WebsiteID){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Story of Love';

        $Users                                      = Auth::user();
        $this->_data['User']                        = $Users;
        $this->_data['WebsiteID']                   = $WebsiteID;


        return Theme::view('modules.storyoflove.story_of_love.list',$this->_data);
    }

    public function showStoryOfLoveWithMsg($WebsiteID,$ScsMsg)
    {
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Story of Love';

        $Users                                      = Auth::user();
        $this->_data['User']                        = $Users;
        $this->_data['WebsiteID']                   = $WebsiteID;
        $this->_data['ScsMsg']                      = base64_decode($ScsMsg);

        return Theme::view('modules.storyoflove.story_of_love.list',$this->_data);
    }

    public function saveStoryOfLove(Request $request){

        $validator = Validator::make($request->all(), [
            'title'                             => 'required',
            'description'                       => 'required',
            'date_story'                        => 'required|date|date_format:d-m-Y|before:today'
        ],[
            'title.required'                    => 'Judul Wajib diisi!',
            'description.required'              => 'Description wajib diisi!',
            'date_story.required'               => 'Tanggal Moment wajib diisi!',
            'date_story.date'                   => 'Format Tanggal Moment salah!',
            'date_story.date_format'            => 'Format Tanggal Moment (Tanggal-Bulan-Tahun)',
            'date_story.before'                 => 'Tanggal anda melebihi tanggal hari ini!',
        ]);

        if ($validator->fails()) {
            $data                                   = array(
                "status"                                => false,
                "message"                               => $validator->errors()->all(),
                "validator"                             => $validator->errors()
            );
        }else{
            if($request->statusform == 'edit'){
                $StoryOfLove                            = StoryOfLoveModel::find($request->storyoflove_id);
            }else{
                $StoryOfLove                            = new StoryOfLoveModel();
            }
            $StoryOfLove->user_id                   = Auth::id();
            $StoryOfLove->website_id                = $request->website_id;
            $StoryOfLove->title                     = $request->title;
            $StoryOfLove->description               = $request->description;
            $StoryOfLove->date_story                = DateFormat($request->date_story,"Y-m-d");
            $StoryOfLove->video                     = $request->video;
            $StoryOfLove->created_by                = Auth::id();
            $StoryOfLove->updated_by                = Auth::id();

            try {
                $StoryOfLove->save();

                ### UPLOAD FOTO ###
                if($request->file('imageFile')){
                    $Image                                      = $request->file('imageFile');
                    $ImageFiles                                 = $this->imageUpload($Image);

                    $StoryOfLoveInfo                            = StoryOfLoveModel::find($StoryOfLove->id);
                    $StoryOfLoveInfo->file                      = $ImageFiles;
                    try {
                        $StoryOfLoveInfo->save();
                        $data                                    = array(
                            "status"                                => true,
                            "message"                               => 'Berhasil! Moment terbaikmu telah tersimpan.',
                            "output"                                => array(
                                "id"                            => $StoryOfLoveInfo->id,
                                "title"                         => $StoryOfLoveInfo->title,
                                "description"                   => $StoryOfLoveInfo->description,
                                "date_story"                    => DateFormat($StoryOfLoveInfo->date_story,"d F Y"),
                                "file"                          => url('/')."/".$this->urlStoryOfLove.$StoryOfLoveInfo->file,
                                "statusform"                    => $request->statusform
                            )
                        );
                    }
                    catch (\Exception $e) {
                        $data                                    = array(
                            "status"                                => false,
                            "message"                               => 'Maaf, ada Kesalah teknis. Mohon hubungi web developer. (email: barindra1988@gmail.com)'
                        );
                        Activity::log([
                            'contentId'   => $StoryOfLove->id,
                            'contentType' => 'Website Story Of Love',
                            'action'      => 'saveStoryOfLove',
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
                        "message"                               => 'Berhasil! Moment terbaikmu telah tersimpan.',
                        "output"                                => array(
                            "id"                            => $StoryOfLove->id,
                            "title"                         => $StoryOfLove->title,
                            "description"                   => $StoryOfLove->description,
                            "date_story"                    => DateFormat($StoryOfLove->date_story,"d F Y"),
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
                    "title"                 => $request->title,
                    "description"           => $request->description,
                    "date_story"            => $request->date_story,
                    "video"                 => $request->video
                );
                Activity::log([
                    'contentId'   => 0,
                    'contentType' => 'Website Story Of Love',
                    'action'      => 'saveStoryOfLove',
                    'description' => $e->getMessage(),
                    'details'     => json_encode($Details),
                    'updated'     => Auth::id(),
                ]);
            }
        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function deleteStoryOfLove(Request $request)
    {
        $StoryOfLoveID                                  = $request->id;
        $StoryOfLove                                    = StoryOfLoveModel::find($StoryOfLoveID);
        if($StoryOfLove){
            $WebsiteID                                      = $StoryOfLove->website_id;
            if($StoryOfLove->file){
                $File                                           = $this->urlStoryOfLove.$StoryOfLove->file;
                $ExistFile                                      = File::exists($File);
                if($ExistFile){
                    try {
                        $DeleteFile                             = File::delete($File);
                        $StoryOfLove->delete();
                        $data                                       = array(
                            "status"                                    => true,
                            "request"                                   => $StoryOfLoveID,
                            "file"                                      => $ExistFile,
                            'website_id'                                => $WebsiteID,
                            "message"                                   => 'Moment kamu berhasil dihapus'
                        );
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $StoryOfLoveID,
                            "file_locate"                               => $File,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $File,
                            'story_of_love_id'                          => $StoryOfLoveID
                        );

                        Activity::log([
                            'contentId'   => $StoryOfLoveID,
                            'contentType' => 'Website Story Of Love',
                            'action'      => 'deleteStoryOfLove',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $StoryOfLoveID,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $StoryOfLoveID,
                        'contentType' => 'Website Story Of Love',
                        'action'      => 'deleteStoryOfLove',
                        'description' => 'File Not Found',
                        'details'     => json_encode($File),
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                try {
                    $StoryOfLove->delete();
                    $data                                       = array(
                        "status"                                    => true,
                        "request"                                   => $StoryOfLoveID,
                        'website_id'                                => $WebsiteID,
                        "message"                                   => 'Moment kamu berhasil dihapus'
                    );
                }catch (\Exception $e) {
                    $data                                       = array(
                        "status"                                    => false,
                        'website_id'                                => $WebsiteID,
                        "request"                                   => $StoryOfLoveID,
                        "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                    );

                    $Details                                = array(
                        'website_id'                                => $WebsiteID,
                        'story_of_love_id'                          => $StoryOfLoveID
                    );

                    Activity::log([
                        'contentId'   => $StoryOfLoveID,
                        'contentType' => 'Website Story Of Love',
                        'action'      => 'deleteStoryOfLove',
                        'description' => $e->getMessage(),
                        'details'     => json_encode($Details),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $StoryOfLoveID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'story_of_love_id'                          => $StoryOfLoveID
            );

            Activity::log([
                'contentId'   => $StoryOfLoveID,
                'contentType' => 'Website Story Of Love',
                'action'      => 'deleteStoryOfLove',
                'description' => "Data Tidak ditemukan di database.",
                'details'     => json_encode($Details),
                'updated'     => Auth::id(),
            ]);

        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function deleteImageStoryOfLove(Request $request){
        $StoryOfLoveID                                  = $request->id;
        $StoryOfLove                                    = StoryOfLoveModel::find($StoryOfLoveID);

        if($StoryOfLove){
            $WebsiteID                                      = $StoryOfLove->website_id;
            if($StoryOfLove->file){
                $File                                           = $this->urlStoryOfLove.$StoryOfLove->file;
                $ExistFile                                      = File::exists($File);
                if($ExistFile){
                    try {
                        $DeleteFile                             = File::delete($File);
                        $StoryOfLove->file                      = "";
                        $StoryOfLove->save();
                        $data                                       = array(
                            "status"                                    => true,
                            "request"                                   => $StoryOfLoveID,
                            "file"                                      => $ExistFile,
                            'website_id'                                => $WebsiteID,
                            "message"                                   => 'Foto moment kamu berhasil dihapus'
                        );
                    }catch (\Exception $e) {
                        $data                                       = array(
                            "status"                                    => false,
                            "request"                                   => $StoryOfLoveID,
                            "file_locate"                               => $File,
                            "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                        );

                        $Details                                = array(
                            'file_locate'                               => $File,
                            'story_of_love_id'                          => $StoryOfLoveID
                        );

                        Activity::log([
                            'contentId'   => $StoryOfLoveID,
                            'contentType' => 'Website Story Of Love',
                            'action'      => 'deleteImageStoryOfLove',
                            'description' => $e->getMessage(),
                            'details'     => json_encode($Details),
                            'updated'     => Auth::id(),
                        ]);
                    }
                }else{
                    $data                                       = array(
                        "status"                                    => false,
                        "request"                                   => $StoryOfLoveID,
                        "file_locate"                               => $File,
                        "message"                                   => 'Maaf, Data tidak ditemukan.'
                    );
                    Activity::log([
                        'contentId'   => $StoryOfLoveID,
                        'contentType' => 'Website Story Of Love',
                        'action'      => 'deleteStoryOfLove',
                        'description' => 'File Not Found',
                        'details'     => json_encode($File),
                        'updated'     => Auth::id(),
                    ]);
                }
            }else{
                try {
                    $StoryOfLove->delete();
                    $data                                       = array(
                        "status"                                    => true,
                        "request"                                   => $StoryOfLoveID,
                        'website_id'                                => $WebsiteID,
                        "message"                                   => 'Moment kamu berhasil dihapus'
                    );
                }catch (\Exception $e) {
                    $data                                       = array(
                        "status"                                    => false,
                        'website_id'                                => $WebsiteID,
                        "request"                                   => $StoryOfLoveID,
                        "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
                    );

                    $Details                                = array(
                        'website_id'                                => $WebsiteID,
                        'story_of_love_id'                          => $StoryOfLoveID
                    );

                    Activity::log([
                        'contentId'   => $StoryOfLoveID,
                        'contentType' => 'Website Story Of Love',
                        'action'      => 'deleteImageStoryOfLove',
                        'description' => $e->getMessage(),
                        'details'     => json_encode($Details),
                        'updated'     => Auth::id(),
                    ]);
                }
            }
        }else{
            $data                                       = array(
                "status"                                    => false,
                "request"                                   => $StoryOfLoveID,
                "message"                                   => 'Maaf, ada Kesalahan teknis. Mohon hubungi web developer'
            );

            $Details                                = array(
                'story_of_love_id'                          => $StoryOfLoveID
            );

            Activity::log([
                'contentId'   => $StoryOfLoveID,
                'contentType' => 'Website Story Of Love',
                'action'      => 'deleteImageStoryOfLove',
                'description' => "Data Tidak ditemukan di database.",
                'details'     => json_encode($Details),
                'updated'     => Auth::id(),
            ]);

        }

        return response($data, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function previewStoryofLove($WebsiteID)
    {
        $this->_data['state']                       = 'preview';
        $this->_data['string_active_menu']          = 'Story of Love';

        $Users                                      = Auth::user();
        $this->_data['User']                        = $Users;
        $this->_data['WebsiteID']                   = $WebsiteID;

        $StoryOfLove                                = StoryOfLoveModel::where('website_id','=',$WebsiteID)->get();
        $this->_data['StoryOfLove']                 = $StoryOfLove;


        return Theme::view('modules.storyoflove.story_of_love.preview',$this->_data);
    }

    public function getStoryOfLove(Request $request){
        $StoryOfLoveID                              = $request->id;
        $StoryOfLove                                = StoryOfLoveModel::find($StoryOfLoveID);
        if($StoryOfLove){
            if($StoryOfLove->file){
                $imageFile                          = $StoryOfLove->file;
                $File                               = url('/').$this->urlStoryOfLove.$StoryOfLove->file;
                $StatusFile                         = 1;
            }else{
                $imageFile                          = "";
                $File                               = "";
                $StatusFile                         = 2;
            }
            $data                                       = array(
                "status"                                    => true,
                "output"                                    => array(
                    "storyoflove_id"                        => $StoryOfLove->id,
                    "title"                                 => $StoryOfLove->title,
                    "description"                           => $StoryOfLove->description,
                    "video"                                 => $StoryOfLove->video,
                    "date_story"                            => DateFormat($StoryOfLove->date_story,"d-m-Y"),
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

    /**
     * Processing Save Image File.
     * @param \Illuminate\Http\UploadedFile Get Uploaded file for save it on server folder
     * @return String saved FileName
     */
    public function imageUpload($file_image){
        if ($file_image->isValid()) {
            $str_fileName = time().'-'.$file_image->getClientOriginalName();
            $file_image->move($this->PathStoryOfLove, $str_fileName);
            return $str_fileName;
        }
    }


}
