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

Route::group(['prefix' => 'thirdmenu'], function () {
    Route::get('/','ThirdMenuController@show')->name('thirdmenu');
    Route::get('/show','ThirdMenuController@show')->name('thirdmenu_show');
    Route::get('/datatables','ThirdMenuController@datatables')->name('thirdmenu_datatables');
    Route::get('/add','ThirdMenuController@add')->name('thirdmenu_add');
    Route::post('/post','ThirdMenuController@post')->name('thirdmenu_post');
    Route::get('/edit/{id}','ThirdMenuController@edit')->name('thirdmenu_edit');
    Route::post('/update','ThirdMenuController@update')->name('thirdmenu_update');
    Route::get('/delete/{id}','ThirdMenuController@delete')->name('thirdmenu_delete');
});
