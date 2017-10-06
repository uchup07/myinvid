<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your module. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['prefix' => 'setting','middleware' => 'auth'], function () {
    Route::get('/', function () {
        redirect('setting/show');
    });
    Route::get('/show','SettingController@show')->name('setting_show');
    Route::post('/show','SettingController@edit')->name('setting_saved');
    Route::get('/delete_logo/{id}','SettingController@actionDeleteLogo')->name('setting_logo_deleted');
    Route::get('/delete_icon/{id}','SettingController@actionDeleteIcon')->name('setting_icon_deleted');
});
