<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GpusController;
use App\Http\Controllers\HddsController;
use App\Http\Controllers\RamsController;
use App\Http\Controllers\SsdsController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AggregateController;
use App\Http\Controllers\PcCompareController;
use App\Http\Controllers\CpuCoolersController;
use App\Http\Controllers\ProcessorsController;
use App\Http\Controllers\GuestAccountController;
use App\Http\Controllers\MotherboardsController;
use App\Http\Controllers\ComputerCasesController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\CompatibilitiesController;
use App\Http\Controllers\PowerSupplyUnitsController;
use App\Http\Controllers\ScreenResolutionsController;
use App\Http\Controllers\TroubleshootArticlesController;
use App\Http\Controllers\BottleneckCalculatorsController;
use App\Http\Controllers\CompatibilitiesCheckersController;

// Define routes for all models (CRUD routes)
Route::apiResource('troubleshoot_articles', TroubleshootArticlesController::class);
Route::apiResource('gpuses', GpusController::class);
Route::apiResource('processors', ProcessorsController::class);
Route::apiResource('motherboards', MotherboardsController::class);
Route::apiResource('rams', RamsController::class);
Route::apiResource('power_supply_units', PowerSupplyUnitsController::class);
Route::apiResource('computer_cases', ComputerCasesController::class);
Route::apiResource('cpu_coolers', CpuCoolersController::class);
Route::apiResource('hdds', HddsController::class);
Route::apiResource('ssds', SsdsController::class);
// Route::apiResource('compatibilities', CompatibilitiesController::class);
Route::apiResource('accounts', AccountsController::class);
Route::apiResource('screen_resolutions', ScreenResolutionsController::class);

// Define the Bottleneck Calculator route
Route::get('/bottlenecks', [BottleneckCalculatorsController::class, 'index']); // For fetching all data
Route::post('/bottleneck', [BottleneckCalculatorsController::class, 'calculateBottleneck']);

// Compatibility checker route
Route::get('compatibility_checker', [CompatibilitiesCheckersController::class, 'check']);

// Route to get data for a specific model type, handled by AggregateController
Route::get('/aggregate/{modelType}', [AggregateController::class, 'getModelData']);

// Routes for the forget password system
Route::post('admin/request-reset', [ForgetPasswordController::class, 'requestReset']);
Route::post('admin/reset-password', [ForgetPasswordController::class, 'resetPassword']);

Route::post('pc_compare', [PcCompareController::class, 'pcCompare']);
Route::get('components', [PcCompareController::class, 'index']);


Route::get('/create-guest-account', [GuestAccountController::class, 'createGuestAccount']);
// Route to create a build for a guest account
Route::post('/guest/create-build', [GuestAccountController::class, 'createGuestBuild']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
