<?php

use App\Http\Controllers\BackOffice\Administration\UserController;
use App\Http\Controllers\BackOffice\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackOffice\PlantController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
  ->name('homePage.index');

Route::group(['middleware' => ['guest']], function () {
  Route::get('/back-office/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
  Route::post('/back-office/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store');
});

// BackOffice
Route::group(['middleware' => ['auth']], function () {
  Route::inertia('/back-office/', 'BackOffice/Dashboard');

  // Plants
  Route::resource('/back-office/plant', PlantController::class);

  // logout url
  Route::delete('/back-office/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

  /* Administration */
  Route::middleware(['can:administrate'])->group(function () {
    // User management related
    Route::resource('/back-office/user', UserController::class);
    Route::post('/back-office/user/list', [UserController::class, 'list'])
      ->name('user.list');
    Route::get('/back-office/user/restore/{id}', [UserController::class, 'restore'])
      ->name('user.restore');
  });
});
