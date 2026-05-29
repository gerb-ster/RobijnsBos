<?php

use App\Http\Controllers\Api\VegetationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group
| assigned the "api" middleware group.
|
*/

/**
 * GET /api/vegetation
 *
 * Returns all vegetation as a JSON array for use by the Digital Twin map-o
 * (dt.robijnsbos.nl).
 */
Route::get('/vegetation', [VegetationController::class, 'index'])
    ->name('api.vegetation.index');

