<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BalanceController;

Route::post('/deposit', [BalanceController::class, 'deposit']);
Route::post('/withdraw', [BalanceController::class, 'withdraw']);
Route::post('/transfer', [BalanceController::class, 'transfer']);
Route::get('/balance/{user_id}', [BalanceController::class, 'balance']);
Route::any('{any}', function() {
    return response()->json([
        'status' => 'error',
        'message' => 'Route not found'
    ], 404);
})->where('any', '.*');
