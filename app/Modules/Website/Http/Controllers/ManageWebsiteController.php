<?php

namespace App\Modules\Website\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;

use Illuminate\Support\Facades\Validator;

use App\Modules\Website\Models\Website as WebsiteModel;


use Theme;
use Auth;
use Activity;
use Debugbar;


class ManageWebsiteController extends Controller
{

    protected $_data                                = array();
    protected $PathWebInfo                          = array();

    public function __construct()
    {
        $this->middleware('permission:website-menu');
        $this->middleware('permission:website-manage');

        $this->PathWebInfo                          = public_path('media/web_information');

        $this->_data['string_menuname']             = 'Website';
    }

    public function datatables(){
        $Website = WebsiteModel::select(['id', 'boy_name', 'girl_name','ceremony_start','wedding_start','domain','is_active'])
            ->where('user_id','=',Auth::id());

        return Datatables::of($Website)
            ->addColumn('href', function ($Website) {
                return '<a href="'.route('manage_wedding_information_detail',$Website->id).'" class="btn btn-info btn-sm">Manage <i class="fa fa-edit"></i></a>';
            })

            ->editColumn('ceremony_start', function ($Website) {
                return DateFormat($Website->ceremony_start,'d F Y H:i:s');
            })

            ->editColumn('wedding_start', function ($Website) {
                return DateFormat($Website->wedding_start,'d F Y H:i:s');
            })

            ->editColumn('is_active', function ($Event) {
                if($Event->is_active == 1){
                    return '<span class="label label-sm label-success">'.get_active($Event->is_active).'</span>';
                }else{
                    return '<span class="label label-sm label-danger">'.get_active($Event->is_active).'</span>';
                }
            })



            ->rawColumns(['href','is_active'])
            ->make(true);
    }

    public function showInformation(){
        $this->_data['state']                       = 'edit';
        $this->_data['string_active_menu']          = 'Manage';

        $Users                                      = Auth::user();
        $this->_data['User']                        = $Users;


        return Theme::view('modules.website.wedding_information.list',$this->_data);
    }

    public function formEditInformation($WebsiteID){
        $this->_data['state']                       = 'edit';
        $this->_data['string_active_menu']          = 'Manage';

        $Users                                      = Auth::user();
        $Website                                    = WebsiteModel::find($WebsiteID);

        $this->_data['User']                        = $Users;
        $this->_data['Website']                     = $Website;

        $this->_data['boy_dob']                     = DateFormat($Website->boy_dob,"d-m-Y");
        $this->_data['girl_dob']                    = DateFormat($Website->girl_dob,"d-m-Y");


        $this->_data['ceremony_start_date']         = DateFormat($Website->ceremony_start,"d-m-Y");
        $this->_data['ceremony_start_time']         = DateFormat($Website->ceremony_start,"H:i");

        $this->_data['ceremony_end_date']           = DateFormat($Website->ceremony_end,"d-m-Y");
        $this->_data['ceremony_end_time']           = DateFormat($Website->ceremony_end,"H:i");


        $this->_data['wedding_start_date']          = DateFormat($Website->wedding_start,"d-m-Y");
        $this->_data['wedding_start_time']          = DateFormat($Website->wedding_start,"H:i");

        $this->_data['wedding_end_date']            = DateFormat($Website->wedding_end,"d-m-Y");
        $this->_data['wedding_end_time']            = DateFormat($Website->wedding_end,"H:i");

        return Theme::view('modules.website.wedding_information.form',$this->_data);
    }

    public function saveInformation(Request $request){
        $validator = Validator::make($request->all(), [
            'boy_name'                  => 'required|max:150',
            'boy_nick_name'             => 'required|max:5',
            'boy_email'                 => 'required|email|max:150',
            'boy_dob'                   => 'required|date|date_format:d-m-Y',
            'girl_name'                 => 'required|max:150',
            'girl_nick_name'            => 'required|max:5',
            'girl_email'                => 'required|email|max:150',
            'girl_dob'                  => 'required|date|date_format:d-m-Y',
            'ceremony_start_date'       => 'required|date|date_format:d-m-Y',
            'ceremony_start_time'       => 'required',
            'ceremony_end_date'         => 'required|date|date_format:d-m-Y|after_or_equal:ceremony_start_date',
            'ceremony_end_time'         => 'required',
            'ceremony_place'            => 'required|max:150',
            'wedding_start_date'        => 'required|date|date_format:d-m-Y',
            'wedding_start_time'        => 'required',
            'wedding_end_date'          => 'required|date|date_format:d-m-Y|after_or_equal:wedding_start_date',
            'wedding_end_time'          => 'required',
            'wedding_place'             => 'required|max:150'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }


        $BoyDateOfBird                                      = DateFormat($request->boy_dob,"Y-m-d");
        $BoyMobile                                          = str_replace(" ","",$request->boy_mobile);

        $GirlDateOfBird                                     = DateFormat($request->girl_dob,"Y-m-d");
        $GirlMobile                                         = str_replace(" ","",$request->girl_mobile);

        $StartCeremonyDate                                  = DateFormat($request->ceremony_start_date,"Y-m-d");
        $StartCeremonyTime                                  = $request->ceremony_start_time;
        $StartCeremony                                      = DateFormat($StartCeremonyDate." ".$StartCeremonyTime,"Y-m-d H:i:s");

        $EndCeremonyDate                                    = DateFormat($request->ceremony_end_date,"Y-m-d");
        $EndCeremonyTime                                    = $request->ceremony_end_time;
        $EndCeremony                                        = DateFormat($EndCeremonyDate." ".$EndCeremonyTime,"Y-m-d H:i:s");

        $StartWeddingDate                                   = DateFormat($request->wedding_start_date,"Y-m-d");
        $StartWeddingTime                                   = $request->wedding_start_time;
        $StartWedding                                       = DateFormat($StartWeddingDate." ".$StartWeddingTime,"Y-m-d H:i:s");


        $EndWeddingDate                                     = DateFormat($request->wedding_end_date,"Y-m-d");
        $EndWeddingTime                                     = $request->wedding_end_time;
        $EndWedding                                         = DateFormat($EndWeddingDate." ".$EndWeddingTime,"Y-m-d H:i:s");

        $WebsiteID                                          = $request->website_id;
        $Website                                            = WebsiteModel::find($WebsiteID);
        $Website->user_id                                   = Auth::id();
        $Website->boy_name                                  = $request->boy_name;
        $Website->boy_nick_name                             = $request->boy_nick_name;
        $Website->boy_email                                 = $request->boy_email;
        $Website->boy_mobile                                = $BoyMobile;
        $Website->boy_dob                                   = $BoyDateOfBird;
        $Website->boy_description                           = $request->boy_description;
        $Website->boy_facebook_url                          = $request->boy_facebook_url;
        $Website->boy_twitter_url                           = $request->boy_twitter_url;
        $Website->boy_instagram_url                         = $request->boy_instagram_url;
        $Website->girl_name                                 = $request->girl_name;
        $Website->girl_nick_name                            = $request->girl_nick_name;
        $Website->girl_email                                = $request->girl_email;
        $Website->girl_mobile                               = $GirlMobile;
        $Website->girl_dob                                  = $GirlDateOfBird;
        $Website->girl_description                          = $request->girl_description;
        $Website->girl_facebook_url                         = $request->girl_facebook_url;
        $Website->girl_twitter_url                          = $request->girl_twitter_url;
        $Website->girl_instagram_url                        = $request->girl_instagram_url;
        $Website->ceremony_start                            = $StartCeremony;
        $Website->ceremony_end                              = $EndCeremony;
        $Website->ceremony_place                            = $request->ceremony_place;
        $Website->ceremony_address                          = $request->ceremony_address;
        $Website->ceremony_googlemaps                       = $request->ceremony_googlemaps;
        $Website->ceremony_waze                             = $request->ceremony_waze;
        $Website->wedding_start                             = $StartWedding;
        $Website->wedding_end                               = $EndWedding;
        $Website->wedding_place                             = $request->wedding_place;
        $Website->wedding_address                           = $request->wedding_address;
        $Website->wedding_googlemaps                        = $request->wedding_googlemaps;
        $Website->wedding_waze                              = $request->wedding_waze;
        $Website->created_by                                = Auth::id();
        $Website->updated_by                                = Auth::id();
        $Website->is_active                                 = 0;
        $Website->inactive                                  = 0;
        $Json                                               = json_encode($Website);
        try {
            $Website->save();

            ### UPLOAD IMAGE BOY ###
            if($request->file('boy_image')){
                $ImageBoy                                   = $request->file('boy_image');
                $ImageBoyFiles                              = $this->imageUpload($ImageBoy);

                $WebsiteInfo                                = WebsiteModel::find($WebsiteID);
                $WebsiteInfo->boy_image                     = $ImageBoyFiles;
                try {
                    $WebsiteInfo->save();
                }
                catch (\Exception $e) {
                    $ErrMsg                                 = "Maaf, ada kesalahan teknis. Silakan hubungi Web administrator [barindra1988@gmail.com]";
                    $this->_data['ErrMsg']                  = $ErrMsg;
                    Activity::log([
                        'contentId'   => $WebsiteID,
                        'contentType' => 'Website Information',
                        'action'      => 'saveInformation',
                        'description' => $e->getMessage(),
                        'details'     => "ada kesalahan pada saat penyimpanan data :". json_encode($ImageBoyFiles),
                        'updated'     => Auth::id(),
                    ]);
                    return redirect()
                        ->route('manage_wedding_information_detail',$WebsiteID)
                        ->with('ErrMsg',$e->getMessage());
                }
            }
            ### UPLOAD IMAGE BOY ###

            ### UPLOAD IMAGE GIRL ###
            if($request->file('girl_image')){
                $ImageGirl                                  = $request->file('girl_image');
                $ImageGirlFiles                             = $this->imageUpload($ImageGirl);

                $WebsiteInfo                                = WebsiteModel::find($WebsiteID);
                $WebsiteInfo->girl_image                    = $ImageGirlFiles;
                try {
                    $WebsiteInfo->save();
                }
                catch (\Exception $e) {
                    $ErrMsg                                 = "Maaf, ada kesalahan teknis. Silakan hubungi Web administrator [barindra1988@gmail.com]";
                    $this->_data['ErrMsg']                  = $ErrMsg;
                    Activity::log([
                        'contentId'   => $WebsiteID,
                        'contentType' => 'Website Information',
                        'action'      => 'saveInformation',
                        'description' => $e->getMessage(),
                        'details'     => "ada kesalahan pada saat penyimpanan data :". json_encode($ImageBoyFiles),
                        'updated'     => Auth::id(),
                    ]);
                    return redirect()
                        ->route('manage_wedding_information_detail',$WebsiteID)
                        ->with('ErrMsg',$e->getMessage());
                }
            }
            ### UPLOAD IMAGE GIRL ###

            return redirect()
                ->route('wedding_gallery_form',$WebsiteID)
                ->with('ScsMsg',"Informasi Pernikahanmu berhasil disimpan.");
        }
        catch (\Exception $e) {
            $ErrMsg                                         = "Maaf, ada kesalahan teknis. Silakan hubungi Web administrator [barindra1988@gmail.com]";
            $this->_data['ErrMsg']                          = $ErrMsg;
            Activity::log([
                'contentId'   => 0,
                'contentType' => 'Website Information',
                'action'      => 'saveInformation',
                'description' => $e->getMessage(),
                'details'     => $Json,
                'updated'     => Auth::id(),
            ]);
            return redirect()
                ->route('create_wedding_information')
                ->with('ErrMsg',$ErrMsg);
        }
    }


    /**
     * Processing Save Image File.
     * @param \Illuminate\Http\UploadedFile Get Uploaded file for save it on server folder
     * @return String saved FileName
     */
    public function imageUpload($file_image){
        if ($file_image->isValid()) {
            $str_fileName = time().'-'.$file_image->getClientOriginalName();
            $file_image->move($this->PathWebInfo, $str_fileName);

            return $str_fileName;
        }
    }
}
