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

Route::group(['prefix' => 'storyoflove','middleware' => 'auth'], function () {
    Route::get('/sol/{WebsiteID}', 'StoryOfLoveController@showStoryOfLove')->name('wedding_storyoflove_form');
    Route::get('/wmsg/{WebsiteID}/{ScsMsg}', 'StoryOfLoveController@showStoryOfLoveWithMsg')->name('wedding_storyoflove_form_withmsg');
    Route::get('/datatables/{WebsiteID}', 'StoryOfLoveController@datatables')->name('wedding_storyoflove_datatables');
    Route::get('/preview/{WebsiteID}', 'StoryOfLoveController@previewStoryofLove')->name('wedding_storyoflove_preview');
    Route::post('/save', 'StoryOfLoveController@saveStoryOfLove')->name('wedding_storyoflove_saved');
    Route::post('/delete', 'StoryOfLoveController@deleteStoryOfLove')->name('wedding_storyoflove_delete');
    Route::post('/delete_image', 'StoryOfLoveController@deleteImageStoryOfLove')->name('wedding_storyoflove_delete_image');
    Route::post('/get', 'StoryOfLoveController@getStoryOfLove')->name('wedding_storyoflove_get');
});
