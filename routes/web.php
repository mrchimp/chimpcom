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

Route::get('/', 'ChimpcomController@index');
Route::get('ajax/respond/json', 'ChimpcomController@respond');
Route::post('ajax/respond/json', 'ChimpcomController@respond');
Route::get('ajax/commands', 'ChimpcomController@commandList');
Route::post('ajax/commands', 'ChimpcomController@commandList');
Route::get('ajax/tabcomplete', 'ChimpcomController@tabComplete');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');
