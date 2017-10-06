<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Modules\Role\Models\Role as RoleModel;

use Theme;
use App\Jobs\SendMail;


class RegisterController extends Controller
{
    protected $_data = array();

    public function showForm(){
        return Theme::view('register',$this->_data);
    }

    public function actionForm(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                              => 'required|max:150',
            'email'                             => 'required|email|max:150|unique:users',
            'password'                          => 'required|confirmed'
        ],[
            'name.required'                     => 'Nama lengkap Wajib diisi!',
            'email.email'                       => 'Format Email Salah',
            'email.unique:users'                => 'Email Sudah terdaftar',
            'password.required'                 => 'Password wajib diisi!',
            'password.confirmed'                => 'Password tidak sama',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        $User                                   = new User();
        $User->name                             = $request->name;
        $User->email                            = $request->email;
        $User->password                         = bcrypt($request->password);
        $User->is_active                        = 1;
        $User->wallet                           = 0;
        $User->wallet_date                      = date('Y-m-d H:i:s');
        $User->wallet_realtime                  = 0;
        $User->wallet_update                    = date('Y-m-d H:i:s');
        $User->up                               = 0;
        $User->email_token                      = base64_encode($request->email);
        try{
            $User->save();
            $Role                               = RoleModel::where('name','=','member')->first();

            ### SET ROLES ###
            DB::table('role_user')->insert([
                'user_id' => $User->id,
                'role_id' => $Role->id
            ]);
            ### SET ROLES ###

//            $EmailFormat                            = EmailTemplateModel::where('name','=','VERIFICATION_REGISTER')->first();
//            $LinkVerificationRegister               = '<a href="'.route("users_request_reset_password",$Users->email_token).'" > '.route("users_request_reset_password",$Users->email_token).'</a>';
//            $Body                                   = $EmailFormat->template;
//            $Body                                   = str_replace("@FULLNAME",$Users->name,$Body);
//            $Body                                   = str_replace("@EMAIL",$Users->email,$Body);
//            $Body                                   = str_replace("@LINKVERIFICATIONREGISTER",$LinkVerificationRegister,$Body);
            $Body                                   = '
            <h2><strong>Selamat datang di <i>Myinvitation.id</i></strong></h2>
            <p>Terima kasih karena telah mendaftar di website kami Wedding Invitation Online Indonesia. Kami berharap dapat membantu menyiapkan undangan pernikahan sesuai keinginamu.<br><br>
            
            Silakan kunjungi website kami di :<br>
            <a href="'.url("/").'">myinvitation.id</a>
            <br><br>
            
            Untuk login sebagai user silakan klik link di bawah ini :<br>
            <a href="'.route("login").'">myinvitation.id</a>
            <br><br>
            
            Terima Kasih
            </p>
            ';
            $EmailParams                            = array(
                'Subject'                               => "Selamat datang di Myinvtiation.id",
                'Views'                                 => "email.register_notification",
                'User'                                  => $User,
                'To'                                    => $request->email,
                'Body'                                  => $Body,
                'attachment'                            => '' // required
            );


//            dispatch(new SendMail($EmailParams));

            return redirect()
                ->route('login')
                ->with('ScsMsg',"Registrasi Berhasil.")
                ->withInput($request->input());

        }catch (\Exception $e) {
            $Detail                             = array(
                "Name"                          => $request->name,
                "Email"                         => $request->email
            );
            Activity::log([
                'contentId'   => 0,
                'contentType' => 'Register User',
                'action'      => 'actionForm',
                'description' => $e->getMessage(),
                'details'     => json_encode($Detail),
                'updated'     => Auth::id(),
            ]);

            return redirect()
                ->route('register_user')
                ->with('ErrMsg',"Maaf, ada kesalahan sistem. Mohon hubungi web developer.(barindra1988@gmail.com)")
                ->withInput($request->input());
        }


    }
}
