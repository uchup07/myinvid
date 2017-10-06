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

Route::group(['prefix' => 'template', 'middleware' => 'auth'], function () {
    Route::get('/show', 'TemplateController@showTemplate')->name('template_show');
    Route::get('/wmsg/{ScsMsg}', 'TemplateController@showTemplateWithMsg')->name('template_form_withmsg');
    Route::get('/datatables', 'TemplateController@datatables')->name('template_datatables');
    Route::get('/preview/{WebsiteID}', 'TemplateController@previewTemplate')->name('template_preview');
    Route::post('/save', 'TemplateController@saveTemplate')->name('template_saved');
    Route::post('/delete', 'TemplateController@deleteTemplate')->name('template_delete');
    Route::post('/delete_image_desktop', 'TemplateController@deleteImageTemplateDesktop')->name('template_delete_image_desktop');
    Route::post('/delete_image_tablet', 'TemplateController@deleteImageTemplateTablet')->name('template_delete_image_tablet');
    Route::post('/delete_image_mobile', 'TemplateController@deleteImageTemplateMobile')->name('template_delete_image_mobile');
    Route::post('/get', 'TemplateController@getTemplate')->name('template_get');
});


Route::group(['prefix' => 'template/wedding', 'middleware' => 'auth'], function () {
    Route::get('/show/{WebsiteID}', 'TemplateWeddingController@showTemplate')->name('wedding_template_show');
    Route::get('/load/{TemplateID}', 'TemplateWeddingController@loadTemplate')->name('wedding_template_load');
    Route::post('/action', 'TemplateWeddingController@actionTemplate')->name('wedding_template_action');
});
