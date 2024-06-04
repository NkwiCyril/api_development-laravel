<?php

use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(EmployeeController::class)
    ->prefix('employees')
    ->group(
        function () {
            Route::get('/', 'index');
            Route::get('{id}', 'show');
            Route::post('store', 'store');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        }
    );
