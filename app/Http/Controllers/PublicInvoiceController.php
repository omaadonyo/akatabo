<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class PublicInvoiceController extends Controller
{
    public function show($id)
    {
        $invoice = Invoice::with(['items', 'company'])->findOrFail($id);
        abort_if(!in_array($invoice->status, ['sent', 'paid', 'overdue']), 404);
        return view('public.invoice', compact('invoice'));
    }
}
