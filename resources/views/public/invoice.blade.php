<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice {{ $invoice->number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto my-10 bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-blue-600 px-8 py-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">INVOICE</h1>
                <p class="text-blue-200 text-sm mt-1">{{ $invoice->number }}</p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold uppercase
                @switch($invoice->status)
                    @case('paid') bg-green-500 text-white @break
                    @case('sent') bg-blue-400 text-white @break
                    @case('draft') bg-gray-400 text-white @break
                    @case('overdue') bg-red-500 text-white @break
                    @default bg-gray-400 text-white
                @endswitch
            ">{{ $invoice->status }}</span>
        </div>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">From</h3>
                @if($invoice->company)
                <p class="text-gray-800 font-semibold">{{ $invoice->company->name }}</p>
                @if($invoice->company->address)<p class="text-gray-600 text-sm">{{ $invoice->company->address }}</p>@endif
                @if($invoice->company->email)<p class="text-gray-600 text-sm">{{ $invoice->company->email }}</p>@endif
                @endif
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bill To</h3>
                @if($invoice->company)
                <p class="text-gray-800 font-semibold">{{ $invoice->company->name }}</p>
                @if($invoice->company->address)<p class="text-gray-600 text-sm">{{ $invoice->company->address }}</p>@endif
                @if($invoice->company->email)<p class="text-gray-600 text-sm">{{ $invoice->company->email }}</p>@endif
                @endif
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-8 grid grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">Invoice Date</span><p class="font-semibold">{{ $invoice->date->format('M d, Y') }}</p></div>
            <div><span class="text-gray-500">Due Date</span><p class="font-semibold">{{ $invoice->due_date->format('M d, Y') }}</p></div>
            <div><span class="text-gray-500">Balance Due</span><p class="font-semibold text-lg text-blue-600">${{ number_format($invoice->balance, 2) }}</p></div>
        </div>

        <table class="w-full mb-8">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="text-left py-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                    <th class="text-center py-3 text-xs font-semibold text-gray-500 uppercase w-16">Qty</th>
                    <th class="text-right py-3 text-xs font-semibold text-gray-500 uppercase w-28">Unit Price</th>
                    <th class="text-right py-3 text-xs font-semibold text-gray-500 uppercase w-28">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr class="border-b border-gray-100">
                    <td class="py-3 text-sm">{{ $item->description }} @if($item->unit)<span class="text-gray-400 text-xs"> / {{ $item->unit }}</span>@endif</td>
                    <td class="py-3 text-sm text-center">{{ $item->quantity }}</td>
                    <td class="py-3 text-sm text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="py-3 text-sm text-right font-medium">${{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="w-72 ml-auto">
            <div class="flex justify-between py-1 text-sm"><span class="text-gray-500">Subtotal</span><span>${{ number_format($invoice->subtotal, 2) }}</span></div>
            @if($invoice->discount > 0)
            <div class="flex justify-between py-1 text-sm"><span class="text-gray-500">Discount</span><span class="text-red-500">-${{ number_format($invoice->discount, 2) }}</span></div>
            @endif
            @if($invoice->tax_rate > 0)
            <div class="flex justify-between py-1 text-sm"><span class="text-gray-500">Tax ({{ $invoice->tax_rate }}%)</span><span>${{ number_format($invoice->tax_amount, 2) }}</span></div>
            @endif
            <div class="flex justify-between py-2 text-lg font-bold text-blue-600 border-t-2 border-blue-600 mt-2">
                <span>Total</span>
                <span>${{ number_format($invoice->total, 2) }}</span>
            </div>
            @if($invoice->paid_amount > 0)
            <div class="flex justify-between py-1 text-sm"><span class="text-gray-500">Paid</span><span class="text-green-600">-${{ number_format($invoice->paid_amount, 2) }}</span></div>
            <div class="flex justify-between py-1 text-lg font-bold"><span>Balance</span><span>${{ number_format($invoice->balance, 2) }}</span></div>
            @endif
        </div>

        @if($invoice->notes)
        <div class="bg-gray-50 rounded-lg p-4 mt-8">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notes</h4>
            <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="text-center text-xs text-gray-400 mt-10 pt-6 border-t border-gray-200">
            Invoice {{ $invoice->number }} | Generated on {{ now()->format('M d, Y') }}
        </div>
    </div>
</div>

</body>
</html>
