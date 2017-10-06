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


Route::group(['prefix' => 'gallery','middleware' => 'auth'], function () {
    Route::get('/im/{WebsiteID}', 'GalleryController@formGallery')->name('wedding_gallery_form');
    Route::get('/load/{WebsiteID}', 'GalleryController@loadGallery')->name('wedding_gallery_load');
    Route::post('/im', 'GalleryController@saveGallery')->name('wedding_gallery_saved');
    Route::post('/upload', 'GalleryController@uploadGallery')->name('wedding_gallery_upload');
});
