<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerformanceAnalysisController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\TradingPlanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [TradeController::class, 'index'])->name('trades.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
Route::get('/trades', [TradeController::class, 'index'])->name('trades.index');
Route::get('/trades/create', [TradeController::class, 'create'])->name('trades.create');
Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');
Route::put('/trades/{trade}', [TradeController::class, 'update'])->name('trades.update');
Route::delete('/trades/{trade}', [TradeController::class, 'destroy'])->name('trades.destroy');
Route::post('/trades/{trade}/subtrades', [TradeController::class, 'storeSubtrade'])->name('trades.subtrades.store');
Route::put('/trades/{trade}/subtrades/{subtrade}', [TradeController::class, 'updateSubtrade'])->name('trades.subtrades.update');
Route::delete('/trades/{trade}/subtrades/{subtrade}', [TradeController::class, 'destroySubtrade'])->name('trades.subtrades.destroy');

Route::get('/plans', [TradingPlanController::class, 'index'])->name('plans.index');
Route::get('/plans/create', [TradingPlanController::class, 'create'])->name('plans.create');
Route::post('/plans', [TradingPlanController::class, 'store'])->name('plans.store');
Route::get('/plans/{plan}/edit', [TradingPlanController::class, 'edit'])->name('plans.edit');
Route::put('/plans/{plan}', [TradingPlanController::class, 'update'])->name('plans.update');
Route::get('/plans/{plan}', [TradingPlanController::class, 'show'])->name('plans.show');
Route::get('/performance', [PerformanceAnalysisController::class, 'index'])->name('performance.index');
Route::get('/performance/{type}/{year}/{period}', [PerformanceAnalysisController::class, 'show'])->name('performance.detail');
Route::post('/performance/{type}/{year}/{period}', [PerformanceAnalysisController::class, 'updateReview'])->name('performance.detail.update');
