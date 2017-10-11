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

Route::group(['prefix' => 'email/format', 'middleware' => 'auth'], function () {
    Route::get('/datatables', 'EmailFormatController@datatables')->name('email_format_datatables');
    Route::get('/show', 'EmailFormatController@showList')->name('email_format_list');

    Route::post('/get', 'EmailFormatController@getFormat')->name('email_format_get');
    Route::post('/save', 'InvoiceController@saveInvoice')->name('invoice_save');
});
