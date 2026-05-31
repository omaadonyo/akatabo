<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Receipt {{ $receipt->number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto my-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-emerald-700 to-emerald-500 px-10 py-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">RECEIPT</h1>
                <p class="text-emerald-200 text-sm mt-1.5 font-mono">{{ $receipt->number }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold
                @switch($receipt->status)
                    @case('issued') bg-green-500/20 text-green-100 @break
                    @case('cancelled') bg-gray-400/20 text-gray-200 @break
                    @default bg-gray-400/20 text-gray-200
                @endswitch
            ">
                <span class="w-1.5 h-1.5 rounded-full
                    @switch($receipt->status)
                        @case('issued') bg-green-400 @break
                        @case('cancelled') bg-gray-300 @break
                        @default bg-gray-300
                    @endswitch
                "></span>
                {{ ucfirst($receipt->status) }}
            </span>
        </div>
    </div>

    <div class="p-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center px-8 py-2.5 border-2 border-emerald-400 text-emerald-600 font-extrabold text-xl tracking-[0.15em] rounded-lg bg-gradient-to-b from-emerald-50 to-white shadow-sm" style="transform: rotate(-3deg);">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                PAID
            </span>
        </div>

        <div class="grid grid-cols-2 gap-10 mb-8">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em] mb-3">From</h3>
                @if($receipt->company)
                <p class="text-gray-900 font-semibold text-base">{{ $receipt->company->name }}</p>
                @if($receipt->company->address)<p class="text-gray-500 text-sm mt-0.5">{{ $receipt->company->address }}</p>@endif
                @if($receipt->company->email)<p class="text-gray-500 text-sm">{{ $receipt->company->email }}</p>@endif
                @endif
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em] mb-3">Received From</h3>
                @if($receipt->customer)
                <p class="text-gray-900 font-semibold text-base">{{ $receipt->customer->name }}</p>
                @if($receipt->customer->address)<p class="text-gray-500 text-sm mt-0.5">{{ $receipt->customer->address }}</p>@endif
                @if($receipt->customer->email)<p class="text-gray-500 text-sm">{{ $receipt->customer->email }}</p>@endif
                @elseif($receipt->company)
                <p class="text-gray-900 font-semibold text-base">{{ $receipt->company->name }}</p>
                @if($receipt->company->address)<p class="text-gray-500 text-sm mt-0.5">{{ $receipt->company->address }}</p>@endif
                @endif
            </div>
        </div>

        <div class="bg-emerald-50/50 rounded-xl p-5 mb-8 grid grid-cols-3 gap-6 text-sm border border-emerald-100/50">
            <div><span class="text-gray-400 text-xs font-medium">Receipt Date</span><p class="font-semibold text-gray-900 mt-1">{{ $receipt->date->format('M d, Y') }}</p></div>
            @if($receipt->invoice)
            <div><span class="text-gray-400 text-xs font-medium">Reference Invoice</span><p class="font-semibold text-gray-900 mt-1">{{ $receipt->invoice->number }}</p></div>
            @endif
            <div><span class="text-gray-400 text-xs font-medium">Total Received</span><p class="font-bold text-xl text-emerald-600 mt-1">UGX {{ number_format($receipt->total, 2) }}</p></div>
        </div>

        @if($receipt->items->count() > 0)
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
                @foreach($receipt->items as $item)
                <tr class="border-b border-gray-50">
                    <td class="py-3.5 text-sm text-gray-800">{{ $item->description }}</td>
                    <td class="py-3.5 text-sm text-center text-gray-500">{{ $item->quantity }}</td>
                    <td class="py-3.5 text-sm text-right text-gray-500">UGX {{ number_format($item->unit_price, 2) }}</td>
                    <td class="py-3.5 text-sm text-right font-semibold text-gray-900">UGX {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="w-72 ml-auto">
            @if($receipt->subtotal != $receipt->total)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Subtotal</span><span>UGX {{ number_format($receipt->subtotal, 2) }}</span></div>
            @endif
            @if($receipt->discount > 0)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Discount</span><span class="text-red-500">-UGX {{ number_format($receipt->discount, 2) }}</span></div>
            @endif
            @if($receipt->tax_rate > 0)
            <div class="flex justify-between py-1.5 text-sm text-gray-600"><span>Tax ({{ $receipt->tax_rate }}%)</span><span>UGX {{ number_format($receipt->tax_amount, 2) }}</span></div>
            @endif
            <div class="flex justify-between py-3 mt-2 text-lg font-bold text-emerald-600 border-t-2 border-emerald-500">
                <span>Total Received</span>
                <span>UGX {{ number_format($receipt->total, 2) }}</span>
            </div>
        </div>

        @if($receipt->notes)
        <div class="bg-gray-50 rounded-xl p-5 mt-8 border border-gray-100">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</h4>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $receipt->notes }}</p>
        </div>
        @endif

        <div class="text-center text-xs text-gray-400 mt-10 pt-6 border-t border-gray-100">
            Receipt {{ $receipt->number }} | Generated {{ now()->format('M d, Y') }}
        </div>
    </div>
</div>
</body>
</html>
