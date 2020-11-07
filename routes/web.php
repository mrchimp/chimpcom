<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ChimpcomController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [ChimpcomController::class, 'index']);
Route::get('ajax/respond/json', [ChimpcomController::class, 'respond']);
Route::post('ajax/respond/json', [ChimpcomController::class, 'respond']);
Route::get('ajax/commands', [ChimpcomController::class, 'commandList']);
Route::post('ajax/commands', [ChimpcomController::class, 'commandList']);
Route::get('ajax/tabcomplete', [ChimpcomController::class, 'tabComplete']);

// Password reset routes...
// Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
// Route::post('password/reset', 'Auth\PasswordController@postReset');

Route::get('blog/{username}', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{username}/{filename}', [BlogController::class, 'show'])->name('blog.show');
