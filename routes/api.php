<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SparepartController;
use App\Http\Controllers\Api\IncomingTransactionController;
use App\Http\Controllers\Api\OutgoingTransactionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserApprovalController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // User Approvals
        Route::get('approvals', [UserApprovalController::class, 'index']);
        Route::put('approvals/{id}/approve', [UserApprovalController::class, 'approve']);
        Route::delete('approvals/{id}/reject', [UserApprovalController::class, 'reject']);

        // Master Data (Full CRUD)
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('suppliers', SupplierController::class);
        Route::post('spareparts', [SparepartController::class, 'store']);
        Route::put('spareparts/{id}', [SparepartController::class, 'update']);
        Route::delete('spareparts/{id}', [SparepartController::class, 'destroy']);
    });

    // Karyawan & Admin Routes (Shared)
    Route::middleware('role:admin,karyawan')->group(function () {
        // Read-only Master Data
        Route::get('spareparts', [SparepartController::class, 'index']);
        Route::get('spareparts/{id}', [SparepartController::class, 'show']);
        Route::get('categories', [CategoryController::class, 'index']); // For dropdowns
        Route::get('suppliers', [SupplierController::class, 'index']); // For dropdowns
        
        // Transaksi Barang Masuk
        Route::get('incoming-transactions', [IncomingTransactionController::class, 'index']);
        Route::post('incoming-transactions', [IncomingTransactionController::class, 'store']);
        
        // Transaksi Barang Keluar
        Route::get('outgoing-transactions', [OutgoingTransactionController::class, 'index']);
        Route::post('outgoing-transactions', [OutgoingTransactionController::class, 'store']);

        // Dashboard & Report
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('reports', [ReportController::class, 'generate']);
    });
});
