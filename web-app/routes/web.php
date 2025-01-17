<?php

use app\Http\Controllers\BackOffice\Administration\UserController;
use app\Http\Controllers\BackOffice\Auth\AuthenticatedSessionController;
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

// main application
Route::group(['middleware' => ['auth']], function () {
  // logout url
  Route::delete('/back-office/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

  /* Administration */
  Route::middleware(['can:administrate'])->group(function () {
    // User management related
    Route::resource('user', UserController::class);
    Route::post('/back-office/user/list', [UserController::class, 'list'])
      ->name('user.list');
    Route::get('/back-office/user/restore/{id}', [UserController::class, 'restore'])
      ->name('user.restore');
  });
});
