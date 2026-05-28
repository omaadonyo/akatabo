<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Receipt {{ $receipt->number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto my-10 bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-amber-600 px-8 py-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">RECEIPT</h1>
                <p class="text-amber-200 text-sm mt-1">{{ $receipt->number }}</p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold uppercase
                @switch($receipt->status)
                    @case('issued') bg-green-500 text-white @break
                    @case('cancelled') bg-gray-400 text-white @break
                    @default bg-gray-400 text-white
                @endswitch
            ">{{ $receipt->status }}</span>
        </div>
    </div>

    <div class="p-8">
        <div class="text-center mb-8">
            <span class="inline-block px-8 py-2 border-2 border-amber-500 text-amber-600 font-bold text-xl tracking-widest rounded" style="transform: rotate(-3deg);">PAID</span>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">From</h3>
                @if($receipt->company)
                <p class="text-gray-800 font-semibold">{{ $receipt->company->name }}</p>
                @if($receipt->company->address)<p class="text-gray-600 text-sm">{{ $receipt->company->address }}</p>@endif
                @if($receipt->company->email)<p class="text-gray-600 text-sm">{{ $receipt->company->email }}</p>@endif
                @endif
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Received From</h3>
                @if($receipt->company)
                <p class="text-gray-800 font-semibold">{{ $receipt->company->name }}</p>
                @if($receipt->company->address)<p class="text-gray-600 text-sm">{{ $receipt->company->address }}</p>@endif
                @endif
            </div>
        </div>

        <div class="bg-amber-50 rounded-lg p-4 mb-8 grid grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">Receipt Date</span><p class="font-semibold">{{ $receipt->date->format('M d, Y') }}</p></div>
            @if($receipt->invoice)
            <div><span class="text-gray-500">Reference Invoice</span><p class="font-semibold">{{ $receipt->invoice->number }}</p></div>
            @endif
        </div>

        @if($receipt->items->count() > 0)
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
                @foreach($receipt->items as $item)
                <tr class="border-b border-gray-100">
                    <td class="py-3 text-sm">{{ $item->description }}</td>
                    <td class="py-3 text-sm text-center">{{ $item->quantity }}</td>
                    <td class="py-3 text-sm text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="py-3 text-sm text-right font-medium">${{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="w-72 ml-auto">
            @if($receipt->subtotal != $receipt->total)
            <div class="flex justify-between py-1 text-sm"><span class="text-gray-500">Subtotal</span><span>${{ number_format($receipt->subtotal, 2) }}</span></div>
            @endif
            @if($receipt->discount > 0)
            <div class="flex justify-between py-1 text-sm"><span class="text-gray-500">Discount</span><span class="text-red-500">-${{ number_format($receipt->discount, 2) }}</span></div>
            @endif
            @if($receipt->tax_rate > 0)
            <div class="flex justify-between py-1 text-sm"><span class="text-gray-500">Tax ({{ $receipt->tax_rate }}%)</span><span>${{ number_format($receipt->tax_amount, 2) }}</span></div>
            @endif
            <div class="flex justify-between py-2 text-lg font-bold text-amber-600 border-t-2 border-amber-600 mt-2">
                <span>Total Received</span>
                <span>${{ number_format($receipt->total, 2) }}</span>
            </div>
        </div>

        @if($receipt->notes)
        <div class="bg-gray-50 rounded-lg p-4 mt-8">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notes</h4>
            <p class="text-sm text-gray-600">{{ $receipt->notes }}</p>
        </div>
        @endif

        <div class="text-center text-xs text-gray-400 mt-10 pt-6 border-t border-gray-200">
            Receipt {{ $receipt->number }} | Generated on {{ now()->format('M d, Y') }}
        </div>
    </div>
</div>

</body>
</html>
