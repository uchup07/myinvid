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

Route::group(['prefix' => 'confirmation','middleware' => 'auth'], function () {
    Route::post('/get', 'ConfirmationController@get_detail')->name('confirmation_get');
    Route::get('/reject/{ConfirmationID}', 'ConfirmationController@reject')->name('confirmation_reject');
    Route::get('/approve/{ConfirmationID}', 'ConfirmationController@approve')->name('confirmation_approve');
});
