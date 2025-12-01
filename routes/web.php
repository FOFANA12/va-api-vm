<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\ActionFundDisbursementController;
use App\Http\Controllers\ActionFundReceiptController;
use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Export\StructureController;
use App\Http\Controllers\Settings\FileTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::get('file-types/{file_type}/download', [FileTypeController::class, 'download'])->name('file-types.download');

    Route::get('templates/selection-mode/download', [ActionController::class, 'downloadSelectionMode'])
        ->name('templates.selection-mode.download');

    Route::get('action-plans/export-to-excel/{actionPlan}',  [ActionPlanController::class, 'exportToExcel'])->name('actionPlan.exportToExcel');
    Route::get('action-fund-receipts/export-to-word/{actionFundReceipt}',  [ActionFundReceiptController::class, 'exportToWord'])->name('actionFundReceipt.exportToWord');
    Route::get('action-fund-disbursements/export-to-word/{actionFundDisbursement}',  [ActionFundDisbursementController::class, 'exportToWord'])->name('actionFundDisbursement.exportToWord');

    //Structure
    Route::get('structure/export/action-plan-to-excel/{structure}',  [StructureController::class, 'exportActionPlanToExcel'])->name('structure.exportActionPlanToExcel');
    Route::get('structure/export/bilan-to-excel/{structure}',  [StructureController::class, 'exportBilanToExcel'])->name('structure.exportBilanToExcel');
    Route::get('structure/export/procurement-plan-to-excel/{structure}/{generateDocumentType}',  [StructureController::class, 'exportProcurementPlanToWord'])->name('structure.exportProcurementPlanToWord');
    Route::get('structure/export/objective-to-excel/{structure}',  [StructureController::class, 'exportObjectiveToWord'])->name('structure.exportObjectiveToWord');
    Route::get('structure/export/objective-decision-to-excel/{structure}',  [StructureController::class, 'exportObjectiveDecisionToWord'])->name('structure.exportObjectiveDecisionToWord');
});
