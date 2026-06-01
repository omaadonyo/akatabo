<?php

use App\Http\Controllers\PublicInvoiceController;
use App\Http\Controllers\PublicQuotationController;
use App\Http\Controllers\PublicReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/app');
    }
    return view('welcome');
});

Route::get('/invoices/{id}/view', [PublicInvoiceController::class, 'show'])->name('public.invoice.show');
Route::get('/receipts/{id}/view', [PublicReceiptController::class, 'show'])->name('public.receipt.show');
Route::get('/quotations/{id}/view', [PublicQuotationController::class, 'show'])->name('public.quotation.show');

Route::get('/backups/{filename}', function ($filename) {
    $path = storage_path('app/backups/' . basename($filename));
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->download($path);
})->middleware(['auth'])->name('backups.download');
