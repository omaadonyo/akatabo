@php
    $receipt = $receipt ?? $this->record;
    $company = $company ?? $receipt->company;
    $customer = $receipt->customer;
    $items = $items ?? $receipt->items;
    $invoice = $receipt->invoice;
@endphp

<div x-data="{ isDark: document.documentElement.classList.contains('dark') }" x-init="() => { const o = new MutationObserver(() => { isDark = document.documentElement.classList.contains('dark') }); o.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] }); }" style="padding: 4px 0; font-family: system-ui, -apple-system, sans-serif;">
    <div :style="'background:'+(isDark?'#1f2937':'#fff')+';border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;border:1px solid '+(isDark?'#374151':'transparent')">
        <div style="background: linear-gradient(135deg, #047857, #10b981); padding: 28px 28px 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 20px; border: 1px solid rgba(255,255,255,0.2);">
                        {{ strtoupper(substr($company?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size: 16px; font-weight: 700; color: #fff;">{{ $company?->name ?? 'Your Company' }}</div>
                        <div style="font-size: 11px; color: rgba(255,255,255,0.65); margin-top: 1px;">{{ $company?->address ?? '' }}</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em;">RECEIPT</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 3px; font-family: 'Courier New', monospace;">{{ $receipt->number }}</div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; padding: 3px 14px; border-radius: 100px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                        <span style="width: 5px; height: 5px; border-radius: 50%; display: inline-block; background: {{ $receipt->status === 'issued' ? '#34d399' : '#9ca3af' }};"></span>
                        <span style="color: #fff;">{{ ucfirst($receipt->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center; padding: 20px 28px 0;">
            <span style="display: inline-block; padding: 6px 28px; font-size: 14px; font-weight: 800; color: #10b981; border: 2.5px solid #10b981; border-radius: 8px; transform: rotate(-3deg); letter-spacing: 0.12em; background: linear-gradient(135deg, #f0fdf4, #ecfdf5);">
                <svg style="width: 16px; height: 16px; display: inline; margin-right: 6px; vertical-align: middle;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                PAID
            </span>
        </div>

        <div style="padding: 20px 28px 8px; display: flex; gap: 16px; margin-bottom: 12px;">
            <div :style="'flex:1;padding:14px 18px;background:'+(isDark?'#064e3b':'#f0fdf4')+';border-radius:10px;border:1px solid '+(isDark?'#10b98180':'#bbf7d0')">
                <div style="font-size: 9px; font-weight: 700; color: #10b981; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">Received From</div>
                <div :style="'font-size:14px;font-weight:600;color:'+(isDark?'#f3f4f6':'#111827')">{{ $customer?->name ?? $company?->name ?? 'N/A' }}</div>
                @if($customer?->address || $customer?->email)
                    <div :style="'font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')+';margin-top:3px'">{{ $customer?->address }}</div>
                    <div :style="'font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')">{{ $customer?->email }}</div>
                @elseif($company?->address || $company?->email)
                    <div :style="'font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')+';margin-top:3px'">{{ $company?->address }}</div>
                    <div :style="'font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')">{{ $company?->email }}</div>
                @endif
            </div>
            <div :style="'padding:14px 20px;background:'+(isDark?'#064e3b':'#f0fdf4')+';border-radius:10px;border:1px solid '+(isDark?'#10b98180':'#bbf7d0')+';min-width:150px;text-align:right'">
                <div :style="'font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')"><span :style="'font-weight:600;color:'+(isDark?'#e5e7eb':'#374151')">Date:</span> {{ $receipt->date?->format('M d, Y') ?? 'N/A' }}</div>
                @if($invoice)
                <div :style="'font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')+';margin-top:5px'"><span :style="'font-weight:600;color:'+(isDark?'#e5e7eb':'#374151')">Invoice:</span> {{ $invoice->number }}</div>
                @endif
            </div>
        </div>

        @if($items->count() > 0)
        <div style="padding: 0 28px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #047857, #10b981);">
                        <th style="text-align: left; padding: 10px 10px 10px 12px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 32px;">#</th>
                        <th style="text-align: left; padding: 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Description</th>
                        <th style="text-align: center; padding: 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 40px;">Qty</th>
                        <th style="text-align: right; padding: 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 80px;">Price</th>
                        <th style="text-align: right; padding: 10px 12px 10px 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 90px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr :style="'border-bottom:1px solid '+(isDark?'#374151':'#f3f4f6')">
                        <td :style="'padding:10px 10px 10px 12px;color:'+(isDark?'#9ca3af':'#6b7280')+';font-size:11px'">{{ $index + 1 }}</td>
                        <td :style="'padding:10px;color:'+(isDark?'#f3f4f6':'#374151')+';font-weight:500'">{{ $item->description }}</td>
                        <td :style="'padding:10px;text-align:center;color:'+(isDark?'#9ca3af':'#6b7280')">{{ number_format($item->quantity, 2) }}</td>
                        <td :style="'padding:10px;text-align:right;color:'+(isDark?'#9ca3af':'#6b7280')">UGX {{ number_format($item->unit_price, 2) }}</td>
                        <td :style="'padding:10px 12px 10px 10px;text-align:right;color:'+(isDark?'#f3f4f6':'#111827')+';font-weight:600'">UGX {{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div style="padding: 8px 28px 24px; display: flex; justify-content: flex-end;">
            <div style="width: 260px;">
                @if($receipt->subtotal != $receipt->total)
                <div :style="'display:flex;justify-content:space-between;font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')+';padding:4px 0'"><span>Subtotal</span><span :style="'font-weight:500;color:'+(isDark?'#e5e7eb':'#374151')">UGX {{ number_format($receipt->subtotal, 2) }}</span></div>
                @endif
                <div :style="'display:flex;justify-content:space-between;font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')+';padding:4px 0'"><span>Tax ({{ number_format($receipt->tax_rate, 1) }}%)</span><span :style="'font-weight:500;color:'+(isDark?'#e5e7eb':'#374151')">UGX {{ number_format($receipt->tax_amount, 2) }}</span></div>
                <div :style="'display:flex;justify-content:space-between;font-size:12px;color:'+(isDark?'#9ca3af':'#6b7280')+';padding:4px 0'"><span>Discount</span><span :style="'font-weight:500;color:'+(isDark?'#e5e7eb':'#374151')">UGX {{ number_format($receipt->discount, 2) }}</span></div>
                <div :style="'display:flex;justify-content:space-between;font-size:14px;font-weight:700;color:'+(isDark?'#6ee7b7':'#047857')+';padding-top:10px;margin-top:6px;border-top:2px solid '+(isDark?'#10b981':'#047857')"><span>Total Received</span><span>UGX {{ number_format($receipt->total, 2) }}</span></div>
            </div>
        </div>

        <div style="padding: 0 28px 24px; display: flex; gap: 20px; align-items: flex-start;">
            @if($company?->receipt_notes)
            <div :style="'flex:1;padding:14px 18px;background:'+(isDark?'#374151':'#f9fafb')+';border-radius:10px;border-left:3px solid '+(isDark?'#6b7280':'#d1d5db')">
                <div :style="'font-size:9px;font-weight:700;color:'+(isDark?'#9ca3af':'#9ca3af')+';text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px'">Notes</div>
                <div :style="'font-size:12px;color:'+(isDark?'#d1d5db':'#6b7280')+';line-height:1.6;white-space:pre-wrap'">{{ $company->receipt_notes }}</div>
            </div>
            @endif
            @isset($qrSvg)
            <div :style="'flex-shrink:0;text-align:center;padding:16px;background:'+(isDark?'#064e3b':'#f0fdf4')+';border:1px solid '+(isDark?'#10b98180':'#bbf7d0')+';border-radius:10px'">
                <img src="{!! $qrSvg !!}" style="width: 80px; height: 80px;">
                <div :style="'font-size:9px;color:'+(isDark?'#9ca3af':'#6b7280')+';margin-top:6px'">Scan to view online</div>
            </div>
            @endisset
        </div>

        @if($receipt->notes)
        <div style="padding: 0 28px 24px;">
            <div :style="'padding:14px 18px;background:'+(isDark?'#374151':'#f9fafb')+';border-radius:10px;border-left:3px solid '+(isDark?'#6b7280':'#d1d5db')">
                <div :style="'font-size:9px;font-weight:700;color:'+(isDark?'#9ca3af':'#9ca3af')+';text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px'">Document Notes</div>
                <div :style="'font-size:12px;color:'+(isDark?'#d1d5db':'#6b7280')+';line-height:1.6;white-space:pre-wrap'">{{ $receipt->notes }}</div>
            </div>
        </div>
        @endif

        <div :style="'text-align:center;padding:14px 28px;border-top:1px solid '+(isDark?'#374151':'#f3f4f6')">
            <div :style="'display:inline-block;padding:6px 20px;background:'+(isDark?'#064e3b':'#f0fdf4')+';border:1px solid '+(isDark?'#10b98180':'#bbf7d0')+';border-radius:20px;font-size:10px;'+(isDark?'color:#6ee7b7;':'color:#047857;')+'font-weight:600'">
                {{ $receipt->date?->format('M d, Y') ?? '' }}
                @if($invoice) &middot; Invoice {{ $invoice->number }} @endif
            </div>
        </div>
    </div>
</div>
