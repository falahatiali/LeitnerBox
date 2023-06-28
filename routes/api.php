<?php

use App\Http\Controllers\Api\User\PermissionController;
use App\Http\Controllers\Api\User\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/auth.php';

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum' , 'can:admin']] , function (){
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
});
