<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Admin Routes
 */
Route::get('admin/login', 'AdminAuth\LoginController@showLoginForm');
Route::post('admin/login', 'AdminAuth\LoginController@login');
Route::match(['GET', 'POST'], 'admin/logout', 'AdminAuth\LoginController@logout');

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['admin', 'history:admin']], function() {
    
    Route::get('dashboard', 'AdminController@dashboard')->name('admin.dashboard');
    Route::get('/', function(){
    	return redirect()->route('admin.dashboard');
    });

    Route::resource('admins', 'AdminController');

    Route::get('admin/admins/list', 'AdminController@list')->name('admins.list');

});

/**
 * Password Resets
 */
Route::get('admin/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm');
Route::post('admin/password/reset', 'AdminAuth\ResetPasswordController@reset');
Route::post('admin/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail');
Route::get('admin/password/reset/{token}', 'AdminAuth\ResetPasswordController@showResetForm');

/**
 * User Routes
 */
Auth::routes();

Route::get('/home', 'HomeController@index');
