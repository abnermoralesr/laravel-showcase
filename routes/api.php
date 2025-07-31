<?php

use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\RegisteredAgentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('companies')->name('companies.')->namespace('App\Http\Controllers\Api\V1\CompanyController')->group(static function (): void {
    Route::post('/', [CompanyController::class, 'store']);
    Route::put('/{company}/registered-agent', [CompanyController::class, 'updateRegisteredAgent']);
});

Route::get('/states/{iso_code}/registered-agent/capacity', [RegisteredAgentController::class, 'verifyCapacity']);
