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

Route::group(['prefix' => 'invoice','middleware' => 'auth'], function () {
    Route::get('/datatables', 'InvoiceController@datatables')->name('invoice_datatables');
    Route::get('/show', 'InvoiceController@listInvoice')->name('invoice_list');
    Route::get('/{InvoiceID}', 'InvoiceController@showInvoice')->name('invoice_show');

    Route::post('/get', 'InvoiceController@getInvoice')->name('invoice_get');
    Route::post('/save', 'InvoiceController@saveInvoice')->name('invoice_save');
});



Route::group(['prefix' => 'invoice/verified','middleware' => 'auth'], function () {
    Route::get('/datatables', 'AdminVerifiedInvoiceController@datatables')->name('invoice_verified_datatables');
    Route::get('/show', 'AdminVerifiedInvoiceController@listInvoice')->name('invoice_verified_list');
    Route::get('/{InvoiceID}', 'InvoiceController@showInvoice')->name('invoice_show');

    Route::post('/get', 'InvoiceController@getInvoice')->name('invoice_get');
    Route::post('/save', 'InvoiceController@saveInvoice')->name('invoice_save');
});
