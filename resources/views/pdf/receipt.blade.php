<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Receipt {{ $receipt->number }}</title>
<style>
    @page { margin: 20mm 15mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #333; line-height: 1.5; }
    .header { border-bottom: 3px solid #d97706; padding-bottom: 15px; margin-bottom: 25px; }
    .header h1 { color: #d97706; font-size: 22pt; margin: 0; }
    .header .number { color: #6b7280; font-size: 11pt; }
    .company-box, .client-box { width: 48%; display: inline-block; vertical-align: top; }
    .company-box h3, .client-box h3 { color: #d97706; font-size: 10pt; margin: 0 0 5px 0; text-transform: uppercase; letter-spacing: 1px; }
    .company-box p, .client-box p { margin: 2px 0; font-size: 9pt; color: #555; }
    .meta { margin: 20px 0; padding: 10px 15px; background: #fffbeb; border-radius: 5px; }
    .meta table { width: 100%; }
    .meta td { padding: 3px 10px; font-size: 9pt; }
    .meta td:first-child { color: #6b7280; }
    .meta td:last-child { font-weight: bold; }
    table.items { width: 100%; border-collapse: collapse; margin: 20px 0; }
    table.items th { background: #d97706; color: #fff; padding: 8px 10px; text-align: left; font-size: 9pt; text-transform: uppercase; }
    table.items td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 9pt; }
    table.items tr:nth-child(even) td { background: #fffbeb; }
    table.items .amount { text-align: right; }
    table.items .qty { text-align: center; }
    .totals { width: 300px; margin-left: auto; }
    .totals table { width: 100%; }
    .totals td { padding: 5px 10px; font-size: 9pt; }
    .totals td:last-child { text-align: right; }
    .totals .grand-total td { font-size: 12pt; font-weight: bold; color: #d97706; border-top: 2px solid #d97706; padding-top: 8px; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #9ca3af; font-size: 8pt; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; color: #fff; }
    .badge-issued { background: #d97706; }
    .badge-cancelled { background: #9ca3af; }
    .paid-stamp { text-align: center; margin: 20px 0; }
    .paid-stamp span { display: inline-block; padding: 8px 30px; border: 3px solid #d97706; color: #d97706; font-size: 18pt; font-weight: bold; letter-spacing: 4px; border-radius: 5px; }
    .qr-section { text-align: center; margin: 30px 0; }
    .qr-section svg { width: 100px; height: 100px; }
    .qr-section p { font-size: 7pt; color: #9ca3af; margin: 5px 0 0; }
    .notes { margin-top: 30px; padding: 15px; background: #f9fafb; border-radius: 5px; font-size: 9pt; color: #555; }
    .notes h4 { margin: 0 0 5px 0; color: #374151; }
</style>
</head>
<body>

<div class="header">
    <table style="width:100%"><tr>
        <td><h1>RECEIPT</h1><span class="number">{{ $receipt->number }}</span></td>
        <td style="text-align:right;"><span class="badge badge-{{ $receipt->status }}">{{ ucfirst($receipt->status) }}</span></td>
    </tr></table>
</div>

<div class="paid-stamp">
    <span>PAID</span>
</div>

<div>
    <div class="company-box">
        <h3>From</h3>
        @if($receipt->company)
            <p><strong>{{ $receipt->company->name }}</strong></p>
            @if($receipt->company->address)<p>{{ $receipt->company->address }}</p>@endif
            @if($receipt->company->email)<p>{{ $receipt->company->email }}</p>@endif
        @endif
    </div>
    <div class="client-box">
        <h3>Received From</h3>
        @if($receipt->company)
            <p><strong>{{ $receipt->company->name }}</strong></p>
            @if($receipt->company->address)<p>{{ $receipt->company->address }}</p>@endif
        @endif
    </div>
</div>

<div class="meta">
    <table>
        <tr><td>Receipt Date</td><td>{{ $receipt->date->format('M d, Y') }}</td></tr>
        @if($receipt->invoice)
        <tr><td>Reference Invoice</td><td>{{ $receipt->invoice->number }}</td></tr>
        @endif
    </table>
</div>

@if($receipt->items->count() > 0)
<table class="items">
    <thead>
        <tr>
            <th style="width:50%">Description</th>
            <th class="qty" style="width:10%">Qty</th>
            <th class="amount" style="width:20%">Unit Price</th>
            <th class="amount" style="width:20%">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receipt->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="qty">{{ $item->quantity }}</td>
            <td class="amount">${{ number_format($item->unit_price, 2) }}</td>
            <td class="amount">${{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="totals">
    <table>
        @if($receipt->subtotal != $receipt->total)
        <tr><td>Subtotal</td><td>${{ number_format($receipt->subtotal, 2) }}</td></tr>
        @endif
        @if($receipt->discount > 0)
        <tr><td>Discount</td><td>-${{ number_format($receipt->discount, 2) }}</td></tr>
        @endif
        @if($receipt->tax_rate > 0)
        <tr><td>Tax ({{ $receipt->tax_rate }}%)</td><td>${{ number_format($receipt->tax_amount, 2) }}</td></tr>
        @endif
        <tr class="grand-total"><td>Total Received</td><td>${{ number_format($receipt->total, 2) }}</td></tr>
    </table>
</div>

@if($receipt->company?->receipt_notes || isset($qrPath))
<table style="width:100%; margin-top: 20px;"><tr>
    @if($receipt->company?->receipt_notes)
    <td style="width:60%; vertical-align:top; padding: 10px 15px; background: #f9fafb; border-radius:5px; font-size: 9pt; color: #555;">
        <h4 style="margin: 0 0 5px 0; color: #d97706; font-size:9pt;">Notes</h4>
        <p style="margin:0;">{{ $receipt->company->receipt_notes }}</p>
    </td>
    @endif
    @isset($qrPath)
    <td style="width:40%; text-align:center; vertical-align:top;">
        <img src="{{ $qrPath }}" alt="QR Code" style="width: 90px; height: 90px;">
        <p style="font-size:7pt; color:#9ca3af; margin:3px 0 0;">Scan to view receipt online</p>
    </td>
    @endisset
</tr></table>
@endif

@if($receipt->notes)
<div class="notes">
    <h4>Document Notes</h4>
    <p>{{ $receipt->notes }}</p>
</div>
@endif

<div class="footer">
    Receipt {{ $receipt->number }} | Generated on {{ now()->format('M d, Y') }}
</div>

</body>
</html>
