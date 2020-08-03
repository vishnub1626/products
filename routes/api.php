<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\ProductImportController;

Route::post('signup', SignUpController::class);

Route::post('login', LoginController::class);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('products/import', ProductImportController::class);
});
