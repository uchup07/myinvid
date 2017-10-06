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

Route::group(['prefix' => 'id'], function () {
    Route::get('/preview/{WebsiteID}/{TemplateID}', 'DemoByIdController@showDemoPreview')->name('demo_preview');
    Route::get('/{WebsiteID}', 'DemoByIdController@showDemo')->name('demo');
});

