<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;

class PublicReceiptController extends Controller
{
    public function show($id)
    {
        $receipt = Receipt::with(['items', 'company', 'customer'])->findOrFail($id);
        return view('public.receipt', compact('receipt'));
    }
}
