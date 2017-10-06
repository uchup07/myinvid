<?php

namespace App\Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


use File;
use App\Modules\Setting\Models\Setting as SettingModel;
use App\User;
use Theme;
use Auth;
use Activity;

class SettingController extends Controller
{
    protected $_data = array();
    protected $destinationPath                      = array();
    protected $destinationPathIcon                  = array();

    public function __construct()
    {
        $this->middleware('permission:setting-view')->only('view');
        $this->middleware('permission:setting-edit')->only('edit');


        $this->_data['string_menuname']                 = 'Admin';
        $this->destinationPath                          = public_path('images/logo');
        $this->destinationPathIcon                      = public_path('images/icon');
    }

    public function show(){
        $Setting                                        = SettingModel::find(1);
        $this->_data['form_name']                       = 'Setting '.$Setting->project_name;
        $this->_data['form']                            = 'setting';

        $this->_data['Setting']                         = $Setting;

        if($Setting->logo){
            $Logo                                       = "images/logo/".$Setting->logo;
            $this->_data['Logos']                       = $Logo;
        }

        if($Setting->icon){
            $Icon                                       = "images/icon/".$Setting->icon;
            $this->_data['Icons']                       = $Icon;
        }


        $this->_data['id']                              = 1;
        $this->_data['User']                            = Auth::user();
        return Theme::view('modules.setting.form',$this->_data);
    }

    public function edit(Request $request){

        $validator = Validator::make($request->all(), [
            'project_name'                  => 'required',
            'title'                         => 'required',
            'keywords'                      => 'required',
            'description'                   => 'required',
            'visi'                          => 'required',
            'misi'                          => 'required',
            'email'                         => 'required|email',
            'address'                       => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }
        $id                                 = $request->id;
        $Setting                            = SettingModel::find($id);
        $Setting->project_name              = $request->project_name;
        $Setting->title                     = $request->title;
        $Setting->meta_keywords             = $request->meta_keywords;
        $Setting->meta_description          = $request->meta_description;
        $Setting->keywords                  = $request->keywords;
        $Setting->description               = $request->description;
        $Setting->visi                      = $request->visi;
        $Setting->misi                      = $request->misi;
        $Setting->email                     = $request->email;
        $Setting->address                   = $request->address;
        $Setting->phone                     = $request->phone;
        $Setting->mobile                    = $request->mobile;
        $Setting->whatsapp                  = $request->whatsapp;
        $Setting->facebook                  = $request->facebook;
        $Setting->twitter                   = $request->twitter;
        $Setting->instagram                 = $request->instagram;
        $Setting->googleplus                = $request->googleplus;
        $Setting->linkedin                  = $request->linkedin;
        $Setting->location                  = $request->location;
        $Setting->latitude                  = $request->latitude;
        $Setting->longitude                 = $request->longitude;
        $Setting->google_analytic           = $request->google_analytic;

        try {

            ### Upload Logo ###
            if($request->file('logo')){
                $File                                       = $request->file('logo');
                try{
                    $LogoFiles                              = $this->imageUpload($File);
                    $Setting->logo                          = $LogoFiles;
                }
                catch (\Exception $e) {
                    Activity::log([
                    		'contentId'   => $id,
                    		'contentType' => 'Setting',
                    		'action'      => 'edit',
                    		'description' => $e->getMessage(),
                    		'details'     => json_encode($File),
                    		'updated'     => Auth::id(),
                    	]);
                }
            }
            ### Upload Logo ###

            ### Upload Icon ###
            if($request->file('icon')){
                $FileIcon                                   = $request->file('icon');
                try{
                    $IconFiles                              = $this->IconUpload($FileIcon);
                    $Setting->icon                          = $IconFiles;
                }
                catch (\Exception $e) {
                    Activity::log([
                    		'contentId'   => $id,
                    		'contentType' => 'Setting',
                    		'action'      => 'edit',
                    		'description' => $e->getMessage(),
                    		'details'     => json_encode($FileIcon),
                    		'updated'     => Auth::id(),
                    	]);
                }
            }
            ### Upload Icon ###
            $Setting->save();
            return redirect()
                ->route('setting_show')
                ->with('ScsMsg',"Perubahan Data berhasil.");
        }
        catch (\Exception $e) {
            $this->_data['ErrMsg']                  = $e->getMessage();
            Activity::log([
            		'contentId'   => $id,
            		'contentType' => 'Setting',
            		'action'      => 'edit',
            		'description' => $e->getMessage(),
            		'details'     => "ada kesalahan pada saat Perubahan data",
            		'updated'     => Auth::id(),
            	]);
            return redirect()
                ->route('setting_show')
                ->with('ErrMsg',$e->getMessage());
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
            $file_image->move($this->destinationPath, $str_fileName);

            return $str_fileName;
        }
    }


    /**
     * Processing Save Image File.
     * @param \Illuminate\Http\UploadedFile Get Uploaded file for save it on server folder
     * @return String saved FileName
     */
    public function IconUpload($file_image){
        if ($file_image->isValid()) {
            $str_fileName = time().'-'.$file_image->getClientOriginalName();
            $file_image->move($this->destinationPathIcon, $str_fileName);

            return $str_fileName;
        }
    }

    public function actionDeleteLogo($id){

        $Setting                                    = SettingModel::find($id);
        $File                                       = "images/logo/".$Setting->logo;
        $ExistFile                                  = File::exists($File);
        if($ExistFile){
            try {
                    $DeleteFile                     = File::delete($File);
                    try {
                            $Setting->logo          = "";
                            $Setting->save();
                            return redirect()
                            ->route('setting_show')
                            ->with('ScsMsg',"Your Logo has been deleted.");
                    }
                    catch (\Exception $e) {
                        Activity::log([
                        		'contentId'   => Auth::id(),
                        		'contentType' => 'Setting',
                        		'action'      => 'delete',
                        		'description' => $e->getMessage(),
                        		'details'     => "Error saat hapus field logo table setting  : id => ". $id,
                        		'updated'     => Auth::id(),
                        	]);
                        return redirect()
                            ->route('setting_show')
                            ->with('ErrMsg',$e->getMessage());
                    }
            }
            catch (\Exception $e) {
                Activity::log([
                		'contentId'   => Auth::id(),
                		'contentType' => 'Setting',
                		'action'      => 'delete',
                		'description' => $e->getMessage(),
                		'details'     => "ada kesalahan pada saat hapus logo :". $File,
                		'updated'     => Auth::id(),
                	]);
                return redirect()
                    ->route('setting_show')
                    ->with('ErrMsg',$e->getMessage());
            }
        }else{
            Activity::log([
                    'contentId'   => Auth::id(),
                    'contentType' => 'Setting',
                    'action'      => 'delete',
                    'description' => "Logo tidak ditemukan",
                    'details'     => "Link tersedia, tetapi file tidak ditemukan ".$File,
                    'language_key'=> 0,
                    'updated'     => Auth::id(),
                ]);
            return redirect()
                ->route('setting_show')
                ->with('ErrMsg',"Logo Tidak ditemukan");
        }
    }

    public function actionDeleteIcon($id){

        $Setting                                    = SettingModel::find($id);
        $File                                       = "images/icon/".$Setting->icon;
        $ExistFile                                  = File::exists($File);
        if($ExistFile){
            try {
                    $DeleteFile                     = File::delete($File);
                    try {
                            $Setting->icon          = "";
                            $Setting->save();
                            return redirect()
                            ->route('setting_show')
                            ->with('ScsMsg',"Your Icon has been deleted.");
                    }
                    catch (\Exception $e) {
                        Activity::log([
                                'contentId'   => Auth::id(),
                                'contentType' => 'Setting',
                                'action'      => 'delete',
                                'description' => $e->getMessage(),
                                'details'     => "Error saat hapus field logo table setting  : id => ". $id,
                                'updated'     => Auth::id(),
                            ]);
                        return redirect()
                            ->route('setting_show')
                            ->with('ErrMsg',$e->getMessage());
                    }
            }
            catch (\Exception $e) {
                Activity::log([
                        'contentId'   => Auth::id(),
                        'contentType' => 'Setting',
                        'action'      => 'delete',
                        'description' => $e->getMessage(),
                        'details'     => "ada kesalahan pada saat hapus icon :". $File,
                        'updated'     => Auth::id(),
                    ]);
                return redirect()
                    ->route('setting_show')
                    ->with('ErrMsg',$e->getMessage());
            }
        }else{
            Activity::log([
                    'contentId'   => Auth::id(),
                    'contentType' => 'Setting',
                    'action'      => 'delete',
                    'description' => "Icon tidak ditemukan",
                    'details'     => "Link tersedia, tetapi file tidak ditemukan ".$File,
                    'language_key'=> 0,
                    'updated'     => Auth::id(),
                ]);
            return redirect()
                ->route('setting_show')
                ->with('ErrMsg',"Icon Tidak ditemukan");
        }
    }

}
