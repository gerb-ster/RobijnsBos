<?php

use App\Http\Controllers\BackOffice\Administration\AreaController;
use App\Http\Controllers\BackOffice\Administration\GroupController;
use App\Http\Controllers\BackOffice\Administration\SpeciesController;
use App\Http\Controllers\BackOffice\Administration\UserController;
use App\Http\Controllers\BackOffice\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackOffice\CommentsController;
use App\Http\Controllers\BackOffice\MutationsController;
use App\Http\Controllers\BackOffice\VegetationController;
use App\Http\Controllers\VegetationController as PublicVegetationController;
use App\Http\Controllers\MutationController as PublicMutationController;
use App\Http\Controllers\CommentController as PublicCommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicVegetationController::class, 'map'])
  ->name('public.vegetation.map');
Route::get('/map/image', [PublicVegetationController::class, 'mapImage'])
  ->name('public.vegetation.map.image');

Route::get('/v/{shortCode}', [PublicVegetationController::class, 'redirect'])
  ->name('public.vegetation.redirect');
Route::get('/vegetation/overview', [PublicVegetationController::class, 'overview'])
  ->name('public.vegetation.overview');

Route::post('/vegetation/list', [PublicVegetationController::class, 'list'])
  ->name('public.vegetation.list');
Route::get('/vegetation/{vegetation}', [PublicVegetationController::class, 'show'])
  ->name('public.vegetation.show');
Route::get('/vegetation/{vegetation}/board.svg', [PublicVegetationController::class, 'showBoard'])
  ->name('public.vegetation.showBoard');

Route::get('/vegetation/{vegetation}/comment/create', [PublicCommentController::class, 'create'])
  ->name('public.vegetation.comment.create');
Route::post('/vegetation/{vegetation}/comment/store', [PublicCommentController::class, 'store'])
  ->name('public.vegetation.comment.store');

Route::group(['middleware' => ['guest']], function () {
  Route::inertia('/account/login', 'Public/Auth/Login')
    ->name('login');
  Route::post('/account/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store');
  Route::inertia('/account/forgot-password', 'Public/Auth/ForgotPassword')
    ->name('forgotPassword');
  Route::post('/account/forgot-password', [AuthenticatedSessionController::class, 'forgotPassword'])
    ->name('forgotPassword.store');
  Route::get('/account/reset-password/{token}', function (string $token) {
    return inertia('Public/Auth/ResetPassword', ['token' => $token]);
  })->middleware('guest')->name('password.reset');
  Route::post('/account/reset-password', [AuthenticatedSessionController::class, 'resetPassword'])
    ->name('password.update');
});

// BackOffice
Route::group(['middleware' => ['auth']], function () {
  // mutation
  Route::get('/vegetation/{vegetation}/mutation/create', [PublicMutationController::class, 'create'])
    ->name('public.vegetation.mutation.create');
  Route::post('/vegetation/{vegetation}/mutation/store', [PublicMutationController::class, 'store'])
    ->name('public.vegetation.mutation.store');

  // logout url
  Route::delete('/account/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

  Route::middleware(['can:accessBackOffice'])->group(function () {
    // Vegetation
    Route::resource('/back-office/vegetation', VegetationController::class);
    Route::post('/back-office/vegetation/list', [VegetationController::class, 'list'])
      ->name('vegetation.list');
    Route::get('/back-office/vegetation/restore/{id}', [VegetationController::class, 'restore'])
      ->name('vegetation.restore');
    Route::get('/back-office/vegetation/{vegetation}/board-download', [VegetationController::class, 'downloadBoard'])
      ->name('vegetation.downloadBoard');

    // Comments
    Route::resource('/back-office/vegetation/{vegetation}/comments', CommentsController::class)
      ->names('comments')
      ->scoped()
      ->parameters([
        'vegetation' => 'vegetation',
        'comment' => 'comment',
      ]);
    Route::post('/back-office/vegetation/{vegetation}/comments/list', [CommentsController::class, 'list'])
      ->name('comments.list');

    // Mutation
    Route::resource('/back-office/vegetation/{vegetation}/mutations', MutationsController::class)
      ->names('mutations')
      ->scoped()
      ->parameters([
        'vegetation' => 'vegetation',
        'mutation' => 'mutation',
      ]);
    Route::post('/back-office/vegetation/{vegetation}/mutations/list', [MutationsController::class, 'list'])
      ->name('mutations.list');

    /* Administration */
    Route::middleware(['can:administrate'])->group(function () {
      // User management related
      Route::resource('/back-office/admin/users', UserController::class);
      Route::post('/back-office/admin/users/list', [UserController::class, 'list'])
        ->name('users.list');
      Route::get('/back-office/admin/users/restore/{id}', [UserController::class, 'restore'])
        ->name('users.restore');

      // Species
      Route::resource('/back-office/admin/species', SpeciesController::class);
      Route::post('/back-office/admin/species/list', [SpeciesController::class, 'list'])
        ->name('species.list');
      Route::get('/back-office/admin/species/restore/{id}', [SpeciesController::class, 'restore'])
        ->name('species.restore');
      Route::get('/back-office/admin/species/{species}/board.svg', [SpeciesController::class, 'showBoard'])
        ->name('species.showBoard');

      // Area
      Route::resource('/back-office/admin/areas', AreaController::class);
      Route::post('/back-office/admin/areas/list', [AreaController::class, 'list'])
        ->name('areas.list');
      Route::get('/back-office/admin/areas/restore/{id}', [AreaController::class, 'restore'])
        ->name('areas.restore');
    });
  });
});
