<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes that do not require authentication
Route::post('students/register', [StudentController::class, 'register']);
Route::post('students/login', [StudentController::class, 'login']);

// Routes that require authentication
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::controller(StudentController::class)
        ->prefix('students')
        ->group(
            function () {
                Route::post('logout', 'logout');
                Route::get('profile', 'profile');
            }
        );

    Route::controller(ProjectController::class)
        ->prefix('projects')
        ->group(
            function () {
                Route::get('/', 'index');
                Route::get('{id}', 'show');
                Route::post('store', 'store');
                Route::put('{id}', 'update');
                Route::delete('{id}', 'destroy');
            }
        );
});
