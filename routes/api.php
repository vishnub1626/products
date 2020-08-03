<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignUpController;

Route::post('signup', SignUpController::class);

Route::post('login', LoginController::class);