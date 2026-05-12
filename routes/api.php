<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BidController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\PurchaseOrderController;
use App\Http\Controllers\Api\V1\PurchaseRequisitionController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\TenderController;
use App\Http\Controllers\Api\V1\VendorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'role:Administrator'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('roles', RoleController::class);

        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::put('/departments/{id}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

        Route::get('/vendors', [VendorController::class, 'index']);
        Route::post('/vendors', [VendorController::class, 'store']);
        Route::put('/vendors/{id}', [VendorController::class, 'update']);
        Route::delete('/vendors/{id}', [VendorController::class, 'destroy']);

        Route::get('/items', [ItemController::class, 'index']);
        Route::post('/items', [ItemController::class, 'store']);
        Route::put('/items/{id}', [ItemController::class, 'update']);
        Route::delete('/items/{id}', [ItemController::class, 'destroy']);

        Route::get('/budgets', [BudgetController::class, 'index']);
        Route::post('/budgets', [BudgetController::class, 'store']);
        Route::put('/budgets/{budget}', [BudgetController::class, 'update']);
        Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy']);

        Route::get('/purchase-requisitions', [PurchaseRequisitionController::class, 'index']);
        Route::post('/purchase-requisitions', [PurchaseRequisitionController::class, 'store']);
        Route::put('/purchase-requisitions/{purchaseRequisition}', [PurchaseRequisitionController::class, 'update']);
        Route::patch('/purchase-requisitions/{purchaseRequisition}/submit', [PurchaseRequisitionController::class, 'submit']);
        Route::patch('/purchase-requisitions/{purchaseRequisition}/approve', [PurchaseRequisitionController::class, 'approve']);
        Route::patch('/purchase-requisitions/{purchaseRequisition}/reject', [PurchaseRequisitionController::class, 'reject']);
        Route::get('/purchase-orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'generatePdf']);
        Route::patch('/purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'markAsSent']);
        // Route::patch('/purchase-orders/{purchaseOrder}/complete', [PurchaseOrderController::class, 'markAsCompleted']);

        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store']);
        Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update']);

        Route::get('/tenders', [TenderController::class, 'index']);
        Route::post('/tenders', [TenderController::class, 'store']);
        Route::put('/tenders/{tender}', [TenderController::class, 'update']);
        Route::patch('/tenders/{tender}/select-winner', [TenderController::class, 'selectWinner']);

        Route::post('/bids', [BidController::class, 'store']);
    });
});
