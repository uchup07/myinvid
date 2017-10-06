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

Route::group(['prefix' => 'website','middleware' => 'auth'], function () {
    Route::get('/create', 'WebsiteCreateController@formInformation')->name('create_wedding_information');
    Route::post('/create', 'WebsiteCreateController@saveInformation')->name('create_wedding_information_saved');
    Route::get('/finish/{id}', 'FinishWebsiteController@showFinish')->name('wedding_finish');
});

Route::group(['prefix' => 'website/manage','middleware' => 'auth'], function () {
    Route::get('/show', 'ManageWebsiteController@showInformation')->name('manage_wedding_information');
    Route::get('/detail/{id}', 'ManageWebsiteController@formEditInformation')->name('manage_wedding_information_detail');
    Route::get('/datatables', 'ManageWebsiteController@datatables')->name('manage_wedding_information_datatables');
    Route::post('/updated', 'ManageWebsiteController@saveInformation')->name('manage_wedding_information_saved');
});
