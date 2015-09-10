<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::get('surf', 'SurfController@index');

Route::group(['namespace' => 'Auth'], function () {
    Route::group(['prefix' => 'register'], function () {
        Route::get('/', 'AuthController@getRegister');
        Route::post('/', 'AuthController@postRegister');
        Route::get('verify/{confirmationCode}', 'AuthController@confirm')->where(['confirmationCode' => '[0-9a-zA-Z]{30}']);
        Route::get('resend', 'AuthController@resend');
        Route::post('resend', 'AuthController@postResend');
    });

    Route::get('login', 'AuthController@getLogin');
    Route::post('login', 'AuthController@postLogin');
    Route::get('logout', 'AuthController@getLogout');

    Route::group(['prefix' => 'password'], function () {
        Route::get('forgot', 'PasswordController@getEmail');
        Route::post('forgot', 'PasswordController@postEmail');
        Route::get('reset/{token}', 'PasswordController@getReset');
        Route::post('reset', 'PasswordController@postReset');
    });
});

Route::group(['prefix' => 'api', 'namespace' => 'Api'], function () {
    Route::get('sites', 'SiteController@getSites');
    Route::post('sites/{id}', 'SiteController@toggleSite');
    Route::delete('sites/{id}', 'SiteController@deleteSite');
    Route::post('sites/new', 'SiteController@addSite');

    Route::get('map', 'MapController@getMap');
});


Route::get('fire', function () {
    // this fires the event
    event(new App\Events\SentGlobalMessage());
    return "event fired";
});
