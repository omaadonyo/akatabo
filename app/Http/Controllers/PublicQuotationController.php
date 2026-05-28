<?php

namespace App\Http\Controllers;

use App\Models\Quotation;

class PublicQuotationController extends Controller
{
    public function show($id)
    {
        $quotation = Quotation::with(['items', 'company', 'customer'])->findOrFail($id);
        abort_if($quotation->status === 'cancelled', 404);
        return view('public.quotation', compact('quotation'));
    }
}
