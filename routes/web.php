<?php

use App\Http\Controllers\PublicInvoiceController;
use App\Http\Controllers\PublicReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invoices/{id}/view', [PublicInvoiceController::class, 'show'])->name('public.invoice.show');
Route::get('/receipts/{id}/view', [PublicReceiptController::class, 'show'])->name('public.receipt.show');
