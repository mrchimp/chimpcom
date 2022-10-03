<?php

use App\Http\Controllers\ChimpcomController;

Route::middleware('auth:sanctum')->post('respond', [ChimpcomController::class, 'respond']);
