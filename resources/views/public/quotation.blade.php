<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Quotation {{ $quotation->number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto my-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-violet-700 to-purple-500 px-10 py-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">QUOTATION</h1>
                <p class="text-violet-200 text-sm mt-1.5 font-mono">{{ $quotation->number }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold
                @switch($quotation->status)
                    @case('accepted') bg-green-500/20 text-green-100 @break
                    @case('sent') bg-blue-400/20 text-blue-100 @break
                    @case('draft') bg-gray-400/20 text-gray-200 @break
                    @case('rejected') bg-red-500/20 text-red-100 @break
                    @case('cancelled') bg-gray-400/20 text-gray-200 @break
                    @default bg-gray-400/20 text-gray-200
                @endswitch
            ">
                <span class="w-1.5 h-1.5 rounded-full
                    @switch($quotation->status)
                        @case('accepted') bg-green-400 @break
                        @case('sent') bg-blue-300 @break
                        @case('draft') bg-gray-300 @break
                        @case('rejected') bg-red-400 @break
                        @case('cancelled') bg-gray-300 @break
                        @default bg-gray-300
                    @endswitch
                "></span>
                {{ ucfirst($quotation->status) }}
            </span>
        </div>
    </div>

    <div class="p-10">
        <div class="grid grid-cols-2 gap-10 mb-8">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em] mb-3">From</h3>
                @if($quotation->company)
                <p class="text-gray-900 font-semibold text-base">{{ $quotation->company->name }}</p>
                @if($quotation->company->address)<p class="text-gray-500 text-sm mt-0.5">{{ $quotation->company->address }}</p>@endif
                @if($quotation->company->email)<p class="text-gray-500 text-sm">{{ $quotation->company->email }}</p>@endif
                @endif
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em] mb-3">Prepared For</h3>
                @if($quotation->customer)
                <p class="text-gray-900 font-semibold text-base">{{ $quotation->customer->name }}</p>
                @if($quotation->customer->address)<p class="text-gray-500 text-sm mt-0.5">{{ $quotation->customer->address }}</p>@endif
                @if($quotation->customer->email)<p class="text-gray-500 text-sm">{{ $quotation->customer->email }}</p>@endif
                @elseif($quotation->company)
                <p class="text-gray-900 font-semibold text-base">{{ $quotation->company->name }}</p>
                @if($quotation->company->address)<p class="text-gray-500 text-sm mt-0.5">{{ $quotation->company->address }}</p>@endif
                @endif
            </div>
        </div>

        <div class="bg-violet-50/50 rounded-xl p-5 mb-8 grid grid-cols-2 gap-6 text-sm border border-violet-100/50">
            <div><span class="text-gray-400 text-xs font-medium">Quotation Date</span><p class="font-semibold text-gray-900 mt-1">{{ $quotation->date->format('M d, Y') }}</p></div>
            <div><span class="text-gray-400 text-xs font-medium">Total Amount</span><p class="font-bold text-xl text-violet-600 mt-1">UGX {{ number_format($quotation->total, 2) }}</p></div>
        </div>

        @if($quotation->items->count() > 0)
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
                @foreach($quotation->items as $item)
                <tr class="border-b border-gray-50">
                    <td class="py-3.5 text-sm text-gray-800">{{ $item->description }} @if($item->unit)<span class="text-gray-400 text-xs"> / {{ $item->unit }}</span>@endif</td>
                    <td class="py-3.5 text-sm text-center text-gray-500">{{ $item->quantity }}</td>
                    <td class="py-3.5 text-sm text-right text-gray-500">UGX {{ number_format($item->unit_price, 2) }}</td>
                    <td class="py-3.5 text-sm text-right font-semibold text-gray-900">UGX {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="w-72 ml-auto">
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Subtotal</span><span>UGX {{ number_format($quotation->subtotal, 2) }}</span></div>
            @if($quotation->discount > 0)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Discount</span><span class="text-red-500">-UGX {{ number_format($quotation->discount, 2) }}</span></div>
            @endif
            @if($quotation->tax_rate > 0)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Tax ({{ $quotation->tax_rate }}%)</span><span>UGX {{ number_format($quotation->tax_amount, 2) }}</span></div>
            @endif
            <div class="flex justify-between py-3 mt-2 text-lg font-bold text-violet-600 border-t-2 border-violet-600">
                <span>Total</span>
                <span>UGX {{ number_format($quotation->total, 2) }}</span>
            </div>
        </div>

        @if($quotation->notes)
        <div class="bg-gray-50 rounded-xl p-5 mt-8 border border-gray-100">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</h4>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $quotation->notes }}</p>
        </div>
        @endif

        <div class="text-center text-xs text-gray-400 mt-10 pt-6 border-t border-gray-100">
            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> Valid for 30 days</span>
        </div>
    </div>
</div>
</body>
</html>
