<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnomalyTypeController;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GeneralSettingController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot', [AuthController::class, 'forgot']);
Route::post('reset', [AuthController::class, 'reset']);

Route::middleware(['api'])->group(function () {
    Route::post('/events/create', [EventController::class, 'store'])->withoutMiddleware("throttle:api");
});

Route::middleware('auth:api')->get('/lists', [DefaultController::class, 'getLists'])
    ->name('defaults.lists');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->post('/roles/toggle_permission/{id}', [RoleController::class, 'togglePermission']);

Route::middleware('auth:api')->group(function () {
    Route::resources([
        'navs'                    => NavController::class,
        'general_settings'        => GeneralSettingController::class,
        'permissions'             => PermissionController::class,
        'users'                   => UserController::class,
        'roles'                   => RoleController::class,
        'anomaly_types'           => AnomalyTypeController::class,
    ]);
    Route::post('auth/password', [AuthController::class, 'changePassword'])->name('auth.change_password');
});