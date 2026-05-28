<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class PublicInvoiceController extends Controller
{
    public function show($id)
    {
        $invoice = Invoice::with(['items', 'company'])->findOrFail($id);
        abort_if($invoice->status === 'cancelled', 404);
        return view('public.invoice', compact('invoice'));
    }
}
