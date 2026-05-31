<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice {{ $invoice->number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto my-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-10 py-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">INVOICE</h1>
                <p class="text-blue-200 text-sm mt-1.5 font-mono">{{ $invoice->number }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold
                @switch($invoice->status)
                    @case('paid') bg-green-500/20 text-green-100 @break
                    @case('sent') bg-blue-400/20 text-blue-100 @break
                    @case('draft') bg-gray-400/20 text-gray-200 @break
                    @case('overdue') bg-red-500/20 text-red-100 @break
                    @default bg-gray-400/20 text-gray-200
                @endswitch
            ">
                <span class="w-1.5 h-1.5 rounded-full
                    @switch($invoice->status)
                        @case('paid') bg-green-400 @break
                        @case('sent') bg-blue-300 @break
                        @case('draft') bg-gray-300 @break
                        @case('overdue') bg-red-400 @break
                        @default bg-gray-300
                    @endswitch
                "></span>
                {{ ucfirst($invoice->status) }}
            </span>
        </div>
    </div>

    <div class="p-10">
        <div class="grid grid-cols-2 gap-10 mb-8">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em] mb-3">From</h3>
                @if($invoice->company)
                <p class="text-gray-900 font-semibold text-base">{{ $invoice->company->name }}</p>
                @if($invoice->company->address)<p class="text-gray-500 text-sm mt-0.5">{{ $invoice->company->address }}</p>@endif
                @if($invoice->company->email)<p class="text-gray-500 text-sm">{{ $invoice->company->email }}</p>@endif
                @endif
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em] mb-3">Bill To</h3>
                @if($invoice->customer)
                <p class="text-gray-900 font-semibold text-base">{{ $invoice->customer->name }}</p>
                @if($invoice->customer->address)<p class="text-gray-500 text-sm mt-0.5">{{ $invoice->customer->address }}</p>@endif
                @if($invoice->customer->email)<p class="text-gray-500 text-sm">{{ $invoice->customer->email }}</p>@endif
                @elseif($invoice->company)
                <p class="text-gray-900 font-semibold text-base">{{ $invoice->company->name }}</p>
                @if($invoice->company->address)<p class="text-gray-500 text-sm mt-0.5">{{ $invoice->company->address }}</p>@endif
                @endif
            </div>
        </div>

        <div class="bg-blue-50/50 rounded-xl p-5 mb-8 grid grid-cols-3 gap-6 text-sm border border-blue-100/50">
            <div><span class="text-gray-400 text-xs font-medium">Invoice Date</span><p class="font-semibold text-gray-900 mt-1">{{ $invoice->date->format('M d, Y') }}</p></div>
            <div><span class="text-gray-400 text-xs font-medium">Due Date</span><p class="font-semibold text-gray-900 mt-1">{{ $invoice->due_date->format('M d, Y') }}</p></div>
            <div><span class="text-gray-400 text-xs font-medium">Balance Due</span><p class="font-bold text-xl text-blue-600 mt-1">UGX {{ number_format($invoice->balance, 2) }}</p></div>
        </div>

        <table class="w-full mb-8">
            <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="text-left py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Description</th>
                    <th class="text-center py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider w-16">Qty</th>
                    <th class="text-right py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider w-28">Price</th>
                    <th class="text-right py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider w-28">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr class="border-b border-gray-50">
                    <td class="py-3.5 text-sm text-gray-800">{{ $item->description }} @if($item->unit)<span class="text-gray-400 text-xs"> / {{ $item->unit }}</span>@endif</td>
                    <td class="py-3.5 text-sm text-center text-gray-500">{{ $item->quantity }}</td>
                    <td class="py-3.5 text-sm text-right text-gray-500">UGX {{ number_format($item->unit_price, 2) }}</td>
                    <td class="py-3.5 text-sm text-right font-semibold text-gray-900">UGX {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="w-72 ml-auto">
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Subtotal</span><span>UGX {{ number_format($invoice->subtotal, 2) }}</span></div>
            @if($invoice->discount > 0)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Discount</span><span class="text-red-500">-UGX {{ number_format($invoice->discount, 2) }}</span></div>
            @endif
            @if($invoice->tax_rate > 0)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Tax ({{ $invoice->tax_rate }}%)</span><span>UGX {{ number_format($invoice->tax_amount, 2) }}</span></div>
            @endif
            <div class="flex justify-between py-3 mt-2 text-lg font-bold text-blue-600 border-t-2 border-blue-600">
                <span>Total</span>
                <span>UGX {{ number_format($invoice->total, 2) }}</span>
            </div>
            @if($invoice->paid_amount > 0)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Paid</span><span class="text-green-600 font-medium">-UGX {{ number_format($invoice->paid_amount, 2) }}</span></div>
            <div class="flex justify-between py-2 text-base font-bold text-gray-900 border-t border-gray-200 mt-1"><span>Balance</span><span>UGX {{ number_format($invoice->balance, 2) }}</span></div>
            @endif
        </div>

        @if($invoice->notes)
        <div class="bg-gray-50 rounded-xl p-5 mt-8 border border-gray-100">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</h4>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="text-center text-xs text-gray-400 mt-10 pt-6 border-t border-gray-100">
            Invoice {{ $invoice->number }} | Generated {{ now()->format('M d, Y') }}
        </div>
    </div>
</div>
</body>
</html>
