<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Quotation {{ $quotation->number }}</title>
<style>
    @page { margin: 18mm 14mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #374151; line-height: 1.6; }
    .header-bar { background: linear-gradient(135deg,#7c3aed,#a855f7); margin: -18mm -14mm 18mm; padding: 22mm 14mm 14mm; color: #fff; }
    .header-bar h1 { font-size: 20pt; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
    .header-bar .number { font-size: 10pt; opacity: 0.7; font-family: 'Courier New', monospace; margin-top: 2px; }
    .header-bar .badge { float: right; padding: 4px 14px; border-radius: 20px; font-size: 8pt; font-weight: 700; text-transform: uppercase; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); }
    .section-grid { display: flex; gap: 24px; margin-bottom: 20px; }
    .section-grid > div { flex: 1; }
    .section-label { font-size: 7.5pt; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 4px; }
    .section-label p { margin: 1px 0; font-size: 8.5pt; color: #6b7280; }
    .meta-bar { background: #f5f3ff; border-radius: 6px; padding: 10px 16px; margin: 16px 0 20px; display: flex; gap: 32px; font-size: 8.5pt; }
    .meta-bar .label { color: #6b7280; }
    .meta-bar .value { font-weight: 600; color: #7c3aed; }
    table.items { width: 100%; border-collapse: collapse; margin: 16px 0; }
    table.items th { background: #7c3aed; color: #fff; padding: 8px 10px; text-align: left; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.06em; }
    table.items th:last-child { text-align: right; }
    table.items th:nth-child(2) { text-align: center; }
    table.items th:nth-child(3) { text-align: right; }
    table.items td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 8.5pt; }
    table.items td:last-child { text-align: right; font-weight: 500; }
    table.items td:nth-child(2) { text-align: center; color: #6b7280; }
    table.items td:nth-child(3) { text-align: right; color: #6b7280; }
    table.items tr:nth-child(even) td { background: #fafafa; }
    .totals { width: 280px; margin-left: auto; }
    .totals table { width: 100%; }
    .totals td { padding: 4px 10px; font-size: 8.5pt; }
    .totals td:last-child { text-align: right; }
    .totals .sub td { color: #6b7280; }
    .totals .grand td { font-size: 11pt; font-weight: 700; color: #7c3aed; border-top: 2px solid #7c3aed; padding-top: 8px; }
    .notes-box { margin-top: 24px; padding: 12px 16px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #d1d5db; font-size: 8pt; color: #6b7280; }
    .notes-box h4 { margin: 0 0 4px; font-size: 8pt; color: #374151; }
    .validity-box { margin-top: 16px; padding: 12px 16px; background: #f5f3ff; border-radius: 6px; border: 1px solid #ddd6fe; font-size: 8pt; color: #6d28d9; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #d1d5db; font-size: 7pt; border-top: 1px solid #f3f4f6; padding-top: 6px; }
</style>
</head>
<body>

<div class="header-bar">
    <span class="badge">{{ ucfirst($quotation->status) }}</span>
    <h1>QUOTATION</h1>
    <div class="number">{{ $quotation->number }}</div>
</div>

<div class="section-grid">
    <div>
        <div class="section-label">From</div>
        @if($quotation->company)
            <p><strong>{{ $quotation->company->name }}</strong></p>
            @if($quotation->company->address)<p>{{ $quotation->company->address }}</p>@endif
            @if($quotation->company->email)<p>{{ $quotation->company->email }}</p>@endif
        @endif
    </div>
    <div>
        <div class="section-label">Prepared For</div>
        @if($quotation->customer)
            <p><strong>{{ $quotation->customer->name }}</strong></p>
            @if($quotation->customer->address)<p>{{ $quotation->customer->address }}</p>@endif
            @if($quotation->customer->email)<p>{{ $quotation->customer->email }}</p>@endif
        @elseif($quotation->company)
            <p><strong>{{ $quotation->company->name }}</strong></p>
        @endif
    </div>
</div>

<div class="meta-bar">
    <div><span class="label">Quotation Date</span><br><span class="value">{{ $quotation->date->format('M d, Y') }}</span></div>
    <div><span class="label">Valid Until</span><br><span class="value">{{ $quotation->date->copy()->addDays(30)->format('M d, Y') }}</span></div>
    <div><span class="label">Total Amount</span><br><span class="value">UGX {{ number_format($quotation->total, 2) }}</span></div>
</div>

<table class="items">
    <tr>
        <th style="width:50%">Description</th>
        <th style="width:8%">Qty</th>
        <th style="width:20%">Price</th>
        <th style="width:22%">Amount</th>
    </tr>
    @foreach($quotation->items as $item)
    <tr>
        <td>{{ $item->description }} @if($item->unit)<br><small style="color:#9ca3af;">per {{ $item->unit }}</small>@endif</td>
        <td>{{ $item->quantity }}</td>
        <td>UGX {{ number_format($item->unit_price, 2) }}</td>
        <td>UGX {{ number_format($item->amount, 2) }}</td>
    </tr>
    @endforeach
</table>

<div class="totals">
    <table>
        <tr class="sub"><td>Subtotal</td><td>UGX {{ number_format($quotation->subtotal, 2) }}</td></tr>
        @if($quotation->discount > 0)
        <tr class="sub"><td>Discount</td><td>-UGX {{ number_format($quotation->discount, 2) }}</td></tr>
        @endif
        @if($quotation->tax_rate > 0)
        <tr class="sub"><td>Tax ({{ $quotation->tax_rate }}%)</td><td>UGX {{ number_format($quotation->tax_amount, 2) }}</td></tr>
        @endif
        <tr class="grand"><td>Total</td><td>UGX {{ number_format($quotation->total, 2) }}</td></tr>
    </table>
</div>

@if($quotation->company?->quotation_notes || isset($qrPath))
<table style="width:100%; margin-top: 20px;"><tr>
    @if($quotation->company?->quotation_notes)
    <td style="width:65%; vertical-align:top; padding: 12px 16px; background: #f9fafb; border-radius:6px; border-left:3px solid #d1d5db;">
        <h4 style="margin: 0 0 4px; font-size: 8pt; color: #374151;">Notes</h4>
        <p style="margin:0; font-size:8pt; color:#6b7280;">{{ $quotation->company->quotation_notes }}</p>
    </td>
    @endif
    @isset($qrPath)
    <td style="width:35%; text-align:center; vertical-align:top;">
        <img src="{{ $qrPath }}" alt="QR Code" style="width: 80px; height: 80px;">
        <p style="font-size:6.5pt; color:#9ca3af; margin:2px 0 0;">Scan to view online</p>
    </td>
    @endisset
</tr></table>
@endif

@if($quotation->notes)
<div class="notes-box">
    <h4>Document Notes</h4>
    <p>{{ $quotation->notes }}</p>
</div>
@endif

<div class="validity-box">
    <strong>Validity:</strong> This quotation is valid for 30 days from the date of issue. Pricing and availability are subject to change.
</div>

<div class="footer">
    Quotation {{ $quotation->number }} | Generated {{ now()->format('M d, Y') }}
</div>

</body>
</html>
