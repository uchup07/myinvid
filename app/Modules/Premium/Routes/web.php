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

Route::group(['prefix' => 'premium', 'middleware' => 'auth'], function () {
    Route::get('/{WebsiteID}', 'PremiumController@FormFeaturePremium')->name('premium_feature_form');
    Route::post('/', 'PremiumController@SaveFeaturePremium')->name('premium_feature_save');
});
