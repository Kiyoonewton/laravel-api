<?php

use App\Http\Controllers\Api\V1\CustomersController;
use App\Http\Controllers\Api\V1\InvoicesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::apiResource('customers', CustomersController::class);
    Route::apiResource('invoices', InvoicesController::class);
});

Route::post("/login", [AuthController::class, 'login']);
Route::post("/logout", [AuthController::class, 'logout']);
Route::post("/register", [AuthController::class, 'register']);

Route::resource("/task", TaskController::class);
