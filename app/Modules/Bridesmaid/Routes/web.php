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

Route::group(['prefix' => 'bridesmaid', 'middleware' => 'auth'], function () {
    Route::get('/show/{WebsiteID}', 'BridesmaidController@showBridesmaid')->name('wedding_bridesmaid_show');
    Route::get('/wmsg/{WebsiteID}/{ScsMsg}', 'BridesmaidController@showBridesmaidWithMsg')->name('wedding_bridesmaid_form_withmsg');
    Route::get('/datatables/{WebsiteID}', 'BridesmaidController@datatables')->name('wedding_bridesmaid_datatables');
    Route::post('/save', 'BridesmaidController@saveBridesmaid')->name('wedding_bridesmaid_saved');
    Route::post('/delete', 'BridesmaidController@deleteBridesmaid')->name('wedding_bridesmaid_delete');
    Route::post('/delete_image', 'BridesmaidController@deleteImageBridesmaid')->name('wedding_bridesmaid_delete_image');
    Route::post('/get', 'BridesmaidController@getBridesmaid')->name('wedding_bridesmaid_get');
});
