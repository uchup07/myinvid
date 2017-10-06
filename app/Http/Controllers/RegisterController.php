<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Theme;
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


    }
}
