<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractDetailController;
use App\Http\Controllers\ContractListController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JurnalCategoryController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\KursController;
use App\Http\Controllers\MoneyChargerController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReimburseController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use App\Models\JurnalCategory;
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

Route::get('/', [HomeController::class, 'index'])->name('dashboard');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['prefix', 'master', 'as' => 'master.'], function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('kurs', KursController::class);
    Route::resource('jurnal-category', JurnalCategoryController::class);
});
Route::group(['prefix' => 'jurnal', 'as' => 'jurnal.', 'controller' => JurnalController::class], function () {
    Route::get('/{id}/create', 'jurnalCreate')->name('detail.create');
    Route::post('getJurnalChart', 'getJurnalChart')->name('getJurnalChart');
    Route::post('/{id}/create', 'jurnalDetailStore')->name('detail.store');
    Route::get('/{balanceId}/{jurnalId}/edit', 'jurnalEdit')->name('detail.edit');
    Route::put('/{balanceId}/{jurnalId}/edit', 'jurnalUpdate')->name('detail.update');
    Route::delete('/{balanceId}/{jurnalId}/delete', 'jurnalDelete')->name('detail.delete');
    Route::get('/{balanceId}/{jurnalId}', 'jurnalShow')->name('detail.show');
    Route::get('/{balanceId}/export', 'jurnalExport')->name('export');
});
Route::resource('jurnal', JurnalController::class);
Route::resource('reimburses', ReimburseController::class);
Route::resource('money-chargers', MoneyChargerController::class);
Route::group(['prefix' => 'contracts', 'as' => 'contracts.', 'controller' => ContractController::class], function () {
    Route::group(['prefix' => '{contract_id}/list', 'as' => 'list.', 'controller' => ContractListController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/create', 'store')->name('store');
        Route::get('{contractListId}/edit', 'edit')->name('edit');
        Route::put('{contractListId}/edit', 'update')->name('update');
        Route::group(['prefix' => '{contractListId}/details', 'as' => 'detail.', 'controller' => ContractDetailController::class], function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/create', 'store')->name('store');
            Route::get('/{contractDetailId}/edit', 'edit')->name('edit');
            Route::put('/{contractDetailId}/update', 'update')->name('update');
        });
    });
});
Route::resource('contracts', ContractController::class);
