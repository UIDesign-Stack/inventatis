<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemLabelController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Pendaftaran publik dimatikan: user baru hanya dibuat operator lewat menu User
Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    // Hak akses user diatur di constructor UserController
    Route::resource('users', UserController::class);

    Route::resource('categories', CategoryController::class)
        ->middleware('permission:category.manage');

    Route::resource('suppliers', SupplierController::class)
        ->middleware('permission:supplier.manage');

    Route::resource('teachers', TeacherController::class)
        ->middleware('permission:teacher.manage');

    // Laporan barang masuk dan cetak label: harus sebelum resource items
    Route::get('items/report', [ItemController::class, 'report'])
        ->middleware('permission:stock-in.report')
        ->name('items.report');

    Route::get('items/labels', ItemLabelController::class)
        ->middleware('permission:item.manage')
        ->name('items.labels');

    Route::resource('items', ItemController::class)
        ->middleware('permission:item.manage');

    // Loan dan report
    Route::get('loans/report', [LoanController::class, 'report'])->name('loans.report');

    // Pengembalian (harus sebelum resource)
    Route::get('loans/returns', [LoanController::class, 'returns'])->name('loans.returns');
    Route::patch('loans/{loan}/return', [LoanController::class, 'returnLoan'])->name('loans.return');

    Route::patch('loans/{loan}/decide', [LoanController::class, 'decide'])->name('loans.decide');
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
});
