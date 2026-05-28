@php
    $invoice = $invoice ?? $this->record;
    $company = $company ?? $invoice->company;
    $items = $items ?? $invoice->items;
    $balance = $invoice->balance;
@endphp

<div style="padding: 4px 0; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 3px solid #f59e0b;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 20px; box-shadow: 0 2px 6px rgba(217,119,6,0.25);">
                {{ strtoupper(substr($company?->name ?? 'A', 0, 1)) }}
            </div>
            <div>
                <div style="font-size: 16px; font-weight: 700; color: #1f2937;">{{ $company?->name ?? 'Your Company' }}</div>
                <div style="font-size: 11px; color: #9ca3af; margin-top: 1px;">{{ $company?->address ?? '123 Business Ave, Suite 100' }}</div>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 24px; font-weight: 800; color: #d97706; letter-spacing: -0.02em;">INVOICE</div>
            <div style="font-size: 12px; color: #6b7280; margin-top: 3px; font-family: 'Courier New', monospace; font-weight: 600;">{{ $invoice->number }}</div>
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                <span style="font-size: 11px; color: #6b7280;">{{ $invoice->date?->format('M d, Y') }}</span>
                <span style="display: inline-block; padding: 2px 12px; font-size: 10px; font-weight: 700; border-radius: 20px; background: {{ match($invoice->status) { 'draft' => '#f3f4f6', 'sent' => '#fef3c7', 'paid' => '#ecfdf5', 'overdue' => '#fef2f2', 'cancelled' => '#f3f4f6', default => '#f3f4f6' } }}; color: {{ match($invoice->status) { 'draft' => '#6b7280', 'sent' => '#d97706', 'paid' => '#16a34a', 'overdue' => '#dc2626', 'cancelled' => '#6b7280', default => '#6b7280' } }};">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Bill To + Dates --}}
    <div style="display: flex; gap: 14px; margin-bottom: 20px;">
        <div style="flex: 1; padding: 14px 18px; background: linear-gradient(135deg, #fffbeb, #fff); border: 1px solid #fde68a; border-radius: 10px;">
            <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">Bill To</div>
            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">{{ $company?->name ?? 'N/A' }}</div>
            @if($company?->address || $company?->email)
                <div style="font-size: 12px; color: #6b7280; margin-top: 3px;">{{ $company?->address }}</div>
                <div style="font-size: 12px; color: #6b7280;">{{ $company?->email }}</div>
            @endif
        </div>
        <div style="padding: 14px 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; min-width: 150px; text-align: right;">
            <div style="font-size: 12px; color: #6b7280;"><span style="font-weight: 600; color: #374151;">Issue:</span> {{ $invoice->date?->format('M d, Y') ?? 'N/A' }}</div>
            <div style="font-size: 12px; color: #6b7280; margin-top: 5px;"><span style="font-weight: 600; color: #374151;">Due:</span> {{ $invoice->due_date?->format('M d, Y') ?? 'N/A' }}</div>
        </div>
    </div>

    {{-- Items table --}}
    <div style="margin-bottom: 16px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
                <tr style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <th style="text-align: left; padding: 9px 10px 9px 12px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 32px;">#</th>
                    <th style="text-align: left; padding: 9px 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Description</th>
                    <th style="text-align: center; padding: 9px 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 40px;">Qty</th>
                    <th style="text-align: center; padding: 9px 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 40px;">Unit</th>
                    <th style="text-align: right; padding: 9px 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 80px;">Price</th>
                    <th style="text-align: right; padding: 9px 12px 9px 10px; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; width: 90px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 10px 10px 10px 12px; color: #9ca3af; font-size: 11px;">{{ $index + 1 }}</td>
                        <td style="padding: 10px; color: #374151; font-weight: 500;">{{ $item->description }}</td>
                        <td style="padding: 10px; text-align: center; color: #6b7280;">{{ number_format($item->quantity, 2) }}</td>
                        <td style="padding: 10px; text-align: center; color: #6b7280;">{{ $item->unit ?? '&mdash;' }}</td>
                        <td style="padding: 10px; text-align: right; color: #6b7280;">${{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding: 10px 12px 10px 10px; text-align: right; color: #111827; font-weight: 600;">${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 32px 0; text-align: center; color: #d1d5db; font-style: italic; font-size: 12px;">No items on this invoice.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
        <div style="width: 250px;">
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; padding: 5px 0;">
                <span>Subtotal</span>
                <span style="color: #374151; font-weight: 500;">${{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; padding: 5px 0;">
                <span>Tax ({{ number_format($invoice->tax_rate, 1) }}%)</span>
                <span style="color: #374151; font-weight: 500;">${{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; padding: 5px 0;">
                <span>Discount</span>
                <span style="color: #374151; font-weight: 500;">${{ number_format($invoice->discount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; padding: 5px 0; border-top: 1px solid #e5e7eb;">
                <span style="font-weight: 600;">Paid</span>
                <span style="color: #16a34a; font-weight: 600;">${{ number_format($invoice->paid_amount, 2) }}</span>
            </div>
            @if($balance > 0)
                <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 5px 0;">
                    <span style="font-weight: 600; color: #dc2626;">Balance Due</span>
                    <span style="font-weight: 700; color: #dc2626;">${{ number_format($balance, 2) }}</span>
                </div>
            @endif
            <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; color: #1f2937; padding-top: 10px; margin-top: 6px; border-top: 2px solid #f59e0b;">
                <span>Total</span>
                <span style="color: #d97706;">${{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- QR Code --}}
    @isset($qrSvg)
        <div style="text-align: center; margin: 24px 0;">
            <div style="display: inline-block; padding: 16px; background: #fff; border: 1px solid #fde68a; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                {!! $qrSvg !!}
                <div style="font-size: 9px; color: #9ca3af; margin-top: 6px;">Scan to view invoice online</div>
            </div>
        </div>
    @endisset

    {{-- Notes --}}
    @if($invoice->notes)
        <div style="padding: 14px 18px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 16px;">
            <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">Notes</div>
            <div style="font-size: 12px; color: #6b7280; line-height: 1.6; white-space: pre-wrap;">{{ $invoice->notes }}</div>
        </div>
    @endif

    {{-- Footer --}}
    <div style="text-align: center; padding-top: 14px; border-top: 1px solid #f3f4f6;">
        <div style="display: inline-block; padding: 6px 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 20px; font-size: 10px; color: #d97706; font-weight: 600;">
            @if($invoice->date) Issued {{ $invoice->date->format('M d, Y') }} @endif
            @if($invoice->due_date) &middot; Due {{ $invoice->due_date->format('M d, Y') }} @endif
        </div>
    </div>
</div>
