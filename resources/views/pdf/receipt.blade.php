<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Receipt {{ $receipt->number }}</title>
<style>
    @page { margin: 18mm 14mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #374151; line-height: 1.6; }
    .header-bar { background: linear-gradient(135deg,#047857,#10b981); margin: -18mm -14mm 18mm; padding: 22mm 14mm 14mm; color: #fff; }
    .header-bar h1 { font-size: 20pt; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
    .header-bar .number { font-size: 10pt; opacity: 0.7; font-family: 'Courier New', monospace; margin-top: 2px; }
    .header-bar .badge { float: right; padding: 4px 14px; border-radius: 20px; font-size: 8pt; font-weight: 700; text-transform: uppercase; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); }
    .paid-ribbon { text-align: center; margin: -8px 0 20px; position: relative; z-index: 1; }
    .paid-ribbon span { display: inline-block; padding: 6px 32px; font-size: 14pt; font-weight: 900; color: #059669; border: 3px solid #10b981; border-radius: 6px; transform: rotate(-3deg); letter-spacing: 0.15em; background: #f0fdf4; }
    .section-grid { display: flex; gap: 24px; margin-bottom: 20px; }
    .section-grid > div { flex: 1; }
    .section-label { font-size: 7.5pt; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 4px; }
    .section-label p { margin: 1px 0; font-size: 8.5pt; color: #6b7280; }
    .meta-bar { background: #f0fdf4; border-radius: 6px; padding: 10px 16px; margin: 16px 0 20px; display: flex; gap: 32px; font-size: 8.5pt; }
    .meta-bar .label { color: #6b7280; }
    .meta-bar .value { font-weight: 600; color: #047857; }
    table.items { width: 100%; border-collapse: collapse; margin: 16px 0; }
    table.items th { background: #047857; color: #fff; padding: 8px 10px; text-align: left; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.06em; }
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
    .totals .grand td { font-size: 11pt; font-weight: 700; color: #047857; border-top: 2px solid #047857; padding-top: 8px; }
    .notes-box { margin-top: 24px; padding: 12px 16px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #d1d5db; font-size: 8pt; color: #6b7280; }
    .notes-box h4 { margin: 0 0 4px; font-size: 8pt; color: #374151; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #d1d5db; font-size: 7pt; border-top: 1px solid #f3f4f6; padding-top: 6px; }
</style>
</head>
<body>

<div class="header-bar">
    <span class="badge">{{ ucfirst($receipt->status) }}</span>
    <h1>RECEIPT</h1>
    <div class="number">{{ $receipt->number }}</div>
</div>

<div class="paid-ribbon">
    <span>PAID</span>
</div>

<div class="section-grid">
    <div>
        <div class="section-label">From</div>
        @if($receipt->company)
            <p><strong>{{ $receipt->company->name }}</strong></p>
            @if($receipt->company->address)<p>{{ $receipt->company->address }}</p>@endif
            @if($receipt->company->email)<p>{{ $receipt->company->email }}</p>@endif
        @endif
    </div>
    <div>
        <div class="section-label">Received From</div>
        @if($receipt->customer)
            <p><strong>{{ $receipt->customer->name }}</strong></p>
            @if($receipt->customer->address)<p>{{ $receipt->customer->address }}</p>@endif
            @if($receipt->customer->email)<p>{{ $receipt->customer->email }}</p>@endif
        @elseif($receipt->company)
            <p><strong>{{ $receipt->company->name }}</strong></p>
        @endif
    </div>
</div>

<div class="meta-bar">
    <div><span class="label">Receipt Date</span><br><span class="value">{{ $receipt->date->format('M d, Y') }}</span></div>
    @if($receipt->invoice)
    <div><span class="label">Reference Invoice</span><br><span class="value">{{ $receipt->invoice->number }}</span></div>
    @endif
    <div><span class="label">Total Received</span><br><span class="value">UGX {{ number_format($receipt->total, 2) }}</span></div>
</div>

@if($receipt->items->count() > 0)
<table class="items">
    <tr>
        <th style="width:50%">Description</th>
        <th style="width:8%">Qty</th>
        <th style="width:20%">Price</th>
        <th style="width:22%">Amount</th>
    </tr>
    @foreach($receipt->items as $item)
    <tr>
        <td>{{ $item->description }}</td>
        <td>{{ $item->quantity }}</td>
        <td>UGX {{ number_format($item->unit_price, 2) }}</td>
        <td>UGX {{ number_format($item->amount, 2) }}</td>
    </tr>
    @endforeach
</table>
@endif

<div class="totals">
    <table>
        @if($receipt->subtotal != $receipt->total)
        <tr class="sub"><td>Subtotal</td><td>UGX {{ number_format($receipt->subtotal, 2) }}</td></tr>
        @endif
        @if($receipt->discount > 0)
        <tr class="sub"><td>Discount</td><td>-UGX {{ number_format($receipt->discount, 2) }}</td></tr>
        @endif
        @if($receipt->tax_rate > 0)
        <tr class="sub"><td>Tax ({{ $receipt->tax_rate }}%)</td><td>UGX {{ number_format($receipt->tax_amount, 2) }}</td></tr>
        @endif
        <tr class="grand"><td>Total Received</td><td>UGX {{ number_format($receipt->total, 2) }}</td></tr>
    </table>
</div>

@if($receipt->company?->receipt_notes || isset($qrPath))
<table style="width:100%; margin-top: 20px;"><tr>
    @if($receipt->company?->receipt_notes)
    <td style="width:65%; vertical-align:top; padding: 12px 16px; background: #f9fafb; border-radius:6px; border-left:3px solid #d1d5db;">
        <h4 style="margin: 0 0 4px; font-size: 8pt; color: #374151;">Notes</h4>
        <p style="margin:0; font-size:8pt; color:#6b7280;">{{ $receipt->company->receipt_notes }}</p>
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

@if($receipt->notes)
<div class="notes-box">
    <h4>Document Notes</h4>
    <p>{{ $receipt->notes }}</p>
</div>
@endif

<div class="footer">
    Receipt {{ $receipt->number }} | Generated {{ now()->format('M d, Y') }}
</div>

</body>
</html>
