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

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
    Route::get('/info','ProfileController@showInfoForm')->name('profile_info');
    Route::post('/info','ProfileController@actionInfoForm')->name('profile_info_saved');
    Route::get('/avatar','ProfileController@showAvatarForm')->name('profile_avatar');
    Route::post('/avatar','ProfileController@actionAvatarForm')->name('profile_avatar_saved');
    Route::get('/delete_avatar','ProfileController@actionDeleteAvatar')->name('profile_avatar_deleted');

    Route::get('/verified_account','ProfileController@showVerifiedForm')->name('profile_verified_account');
    Route::post('/sendcode','ProfileController@sendCodeVerified')->name('profile_sendcode_verified');
    Route::post('/checkcode','ProfileController@checkCodeVerified')->name('profile_checkcode_verified');

});
