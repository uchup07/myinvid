<?php

namespace App\Modules\User\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


use File;
use App\Modules\User\Models\Verified_code as VerifiedCodeModel;
use App\User;
use Theme;
use Auth;
use Activity;


class ProfileController extends Controller
{
    protected $_data                                = array();
    protected $destinationPath                      = array();
    public function __construct()
    {
        $this->_data['string_menuname']             = 'Profile';
        $this->destinationPath                      = public_path('images/user');

    }

    public function showInfoForm(){
        $this->_data['form']                        = 'info';
        $this->_data['form_name']                   = 'Member Info';

        $this->_data['User']                        = Auth::user();
        return Theme::view('modules.user.profile.info',$this->_data);
    }

    public function actionInfoForm(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                  => 'required',
            'place_of_bird'         => 'required',
            'date_of_bird'          => 'required',
            'address'               => 'required',
            'city'                  => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        $User                                       = User::find($request->id);
        $User->name                                 = $request->name;
        $User->place_of_bird                        = $request->place_of_bird;
        $User->date_of_bird                         = DateFormat($request->date_of_bird,"Y-m-d");
        $User->address                              = $request->address;
        $User->city                                 = $request->city;

        try {
            $User->save();
            return redirect()
                ->route('profile_info')
                ->with('ScsMsg',"Your Profile Info has been updated.");
        }
        catch (\Exception $e) {
            $this->_data['ErrMsg']                  = $e->getMessage();
            Activity::log([
            		'contentId'   => $request->id,
            		'contentType' => 'Member Info',
            		'action'      => 'save',
            		'description' => $e->getMessage(),
            		'details'     => "ada kesalahan pada saat input data",
            		'updated'     => Auth::id(),
            	]);
            return redirect()
                ->route('profile_info')
                ->with('ErrMsg',$e->getMessage());
        }
    }

    public function showAvatarForm(){
        $this->_data['form']                        = 'avatar';
        $this->_data['form_name']                   = 'Avatar';

        $User                                       = Auth::user();
        $this->_data['User']                        = $User;
        if($User->image){
            $Avatar                                 = "images/user/".$User->image;
            $this->_data['Avatar']                  = $Avatar;
        }
        return Theme::view('modules.user.profile.avatar',$this->_data);
    }


    public function actionAvatarForm(Request $request){
        $File                                       = $request->file('avatar');
        $ImageFiles                                 = $this->imageUpload($File);

        $User                                       = User::find($request->id);
        $User->image                                = $ImageFiles;

        try {
            $User->save();
            return redirect()
                ->route('profile_avatar')
                ->with('ScsMsg',"Your Avatar has been updated.");
        }
        catch (\Exception $e) {
            $this->_data['ErrMsg']                  = $e->getMessage();
            Activity::log([
            		'contentId'   => $request->id,
            		'contentType' => 'Avatar',
            		'action'      => 'save',
            		'description' => $e->getMessage(),
            		'details'     => "ada kesalahan pada saat penyimpanan data :". $File,
            		'updated'     => Auth::id(),
            	]);
            return redirect()
                ->route('profile_avatar')
                ->with('ErrMsg',$e->getMessage());
        }
    }

    public function actionDeleteAvatar(){
        $User                                       = Auth::user();
        $File                                       =  "images/user/".$User->image;
        $ExistFile                                  = File::exists($File);
        if($ExistFile){
            try {
                    $DeleteFile                     = File::delete($File);
                    try {
                            $Users                  = User::find(Auth::id());
                            $Users->image           = "";
                            $Users->save();
                            return redirect()
                            ->route('profile_avatar')
                            ->with('ScsMsg',"Your Avatar has been deleted.");
                    }
                    catch (\Exception $e) {
                        Activity::log([
                        		'contentId'   => Auth::id(),
                        		'contentType' => 'Avatar',
                        		'action'      => 'delete',
                        		'description' => $e->getMessage(),
                        		'details'     => "Error saat hapus field image table user  : id => ". Auth::id(),
                        		'updated'     => Auth::id(),
                        	]);
                        return redirect()
                            ->route('profile_avatar')
                            ->with('ErrMsg',$e->getMessage());
                    }
            }
            catch (\Exception $e) {
                Activity::log([
                		'contentId'   => Auth::id(),
                		'contentType' => 'Avatar',
                		'action'      => 'delete',
                		'description' => $e->getMessage(),
                		'details'     => "ada kesalahan pada saat hapus avatar :". $File,
                		'updated'     => Auth::id(),
                	]);
                return redirect()
                    ->route('profile_avatar')
                    ->with('ErrMsg',$e->getMessage());
            }
        }else{
            Activity::log([
                    'contentId'   => Auth::id(),
                    'contentType' => 'Avatar',
                    'action'      => 'delete',
                    'description' => "Foto tidak ditemukan",
                    'details'     => "Link tersedia, tetapi file tidak ditemukan ".$File,
                    'language_key'=> 0,
                    'updated'     => Auth::id(),
                ]);
            return redirect()
                ->route('profile_avatar')
                ->with('ErrMsg',"Foto Tidak ditemukan");
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

    public function showVerifiedForm(){
        $this->_data['form']                        = 'verified_account';
        $this->_data['form_name']                   = 'Verified Account';

        $this->_data['User']                        = Auth::user();
        return Theme::view('modules.user.profile.verified',$this->_data);
    }

    public function sendCodeVerified(Request $request){
        $Mobile                                     = $request->mobile;
        if($Mobile){
            $Mobile                                 = str_replace("+62","0",$Mobile);

            $CreateCode                             = $this->CreateCode($Mobile);
            if($CreateCode){
                $SendSMS                            = SendSMS($Mobile,$CreateCode);
                $data                                    = array(
                    "status"                                => true,
                    "message"                               => $SendSMS
                );
            }else{
                $data                                    = array(
                    "status"                                => false,
                    "message"                               => 'Error CreateCode'
                );
            }

        }else{
            $data                                    = array(
                "status"                                => false,
                "message"                               => 'Wrong Number'
            );
        }
        return response($data, 200)
        ->header('Content-Type', 'text/plain');

    }

    protected function CreateCode($Mobile){
        $digits                                     = 4;
        $ResultDigit                                = rand(pow(10, $digits-1), pow(10, $digits)-1);

        $selectedTime                               = date("d-m-Y H:i:s");
        $endTime                                    = strtotime("+5 minutes", strtotime($selectedTime));

        $VerifiedCode                               = new VerifiedCodeModel;

        $VerifiedCode->mobile                       = $Mobile;
        $VerifiedCode->code                         = $ResultDigit;
        $VerifiedCode->expired_code                 = DateFormat($endTime,"Y-m-d H:i:s");
        $VerifiedCode->user_id                      = Auth::id();
        $VerifiedCode->created_by                   = Auth::id();
        $VerifiedCode->updated_by                   = Auth::id();
        try {
            $VerifiedCode->save();
            return $ResultDigit;
        }catch (\Exception $e) {
            Activity::log([
            		'contentId'   => $request->id,
            		'contentType' => 'Verified Code',
            		'action'      => 'verified',
            		'description' => $e->getMessage(),
            		'details'     => "ada kesalahan pada saat calculate date & simpan ke table verified_codes",
            		'updated'     => Auth::id(),
            	]);
            return false;
        }
    }

    public function checkCodeVerified(Request $request){
        $Code                                       = $request->code;
        $VerifiedCode                               = VerifiedCodeModel::where('code','=',$Code)
                                                                            ->where('user_id','=',Auth::id())->get()->first();
        $DateTime1                                  = date('Y-m-d H:i:s');
        $DateTime2                                  = $VerifiedCode->expired_code;

        $Date1                                      = strtotime($DateTime1);
        $Date2                                      = strtotime($DateTime2);
        if($Date1 < $Date2){
            $data                                    = array(
                "status"                                => true,
                "message"                               => 'OK'
            );
        }else{
            $data                                    = array(
                "status"                                => false,
                "message"                               => 'Code Expired'
            );
        }
        return response($data, 200)
        ->header('Content-Type', 'text/plain');
    }
}
