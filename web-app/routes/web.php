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
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
  ->name('homePage.index');

Route::get('/v/{shortCode}', [PublicVegetationController::class, 'redirect'])
  ->name('public.vegetation.redirect');
Route::get('/vegetation/{vegetation}', [PublicVegetationController::class, 'show'])
  ->name('public.vegetation.show');

Route::group(['middleware' => ['guest']], function () {
  Route::get('/back-office/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
  Route::post('/back-office/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store');
});

// BackOffice
Route::group(['middleware' => ['auth']], function () {
  Route::inertia('/back-office/', 'BackOffice/Dashboard');

  // Vegetation
  Route::resource('/back-office/vegetation', VegetationController::class);
  Route::post('/back-office/vegetation/list', [VegetationController::class, 'list'])
    ->name('vegetation.list');
  Route::get('/back-office/vegetation/restore/{id}', [VegetationController::class, 'restore'])
    ->name('vegetation.restore');

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


  // logout url
  Route::delete('/back-office/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

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

    // Area
    Route::resource('/back-office/admin/areas', AreaController::class);
    Route::post('/back-office/admin/areas/list', [AreaController::class, 'list'])
      ->name('areas.list');
    Route::get('/back-office/admin/areas/restore/{id}', [AreaController::class, 'restore'])
      ->name('areas.restore');

    // Group
    Route::resource(
      '/back-office/admin/areas/{area}/groups',
      GroupController::class
    )
      ->names('groups')
      ->scoped()
      ->parameters([
        'area' => 'area',
        'group' => 'group',
      ]);
  });
});
