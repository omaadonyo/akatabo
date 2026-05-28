<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Quotation {{ $quotation->number }}</title>
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
    .badge-accepted { background: #16a34a; }
    .badge-draft { background: #6b7280; }
    .badge-sent { background: #d97706; }
    .badge-rejected { background: #dc2626; }
    .badge-cancelled { background: #9ca3af; }
    .validity { margin-top: 30px; padding: 15px; background: #fffbeb; border-radius: 5px; border: 1px solid #fde68a; font-size: 9pt; }
    .notes { margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 5px; font-size: 9pt; color: #555; }
    .notes h4 { margin: 0 0 5px 0; color: #374151; }
</style>
</head>
<body>

<div class="header">
    <table style="width:100%"><tr>
        <td><h1>QUOTATION</h1><span class="number">{{ $quotation->number }}</span></td>
        <td style="text-align:right;"><span class="badge badge-{{ $quotation->status }}">{{ ucfirst($quotation->status) }}</span></td>
    </tr></table>
</div>

<div>
    <div class="company-box">
        <h3>From</h3>
        @if($quotation->company)
            <p><strong>{{ $quotation->company->name }}</strong></p>
            @if($quotation->company->address)<p>{{ $quotation->company->address }}</p>@endif
            @if($quotation->company->email)<p>{{ $quotation->company->email }}</p>@endif
            @if($quotation->company->phone)<p>{{ $quotation->company->phone }}</p>@endif
        @endif
    </div>
    <div class="client-box">
        <h3>Prepared For</h3>
        @if($quotation->company)
            <p><strong>{{ $quotation->company->name }}</strong></p>
            @if($quotation->company->address)<p>{{ $quotation->company->address }}</p>@endif
        @endif
    </div>
</div>

<div class="meta">
    <table>
        <tr><td>Quotation Date</td><td>{{ $quotation->date->format('M d, Y') }}</td></tr>
        <tr><td>Valid Until</td><td>{{ $quotation->date->copy()->addDays(30)->format('M d, Y') }}</td></tr>
    </table>
</div>

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
        @foreach($quotation->items as $item)
        <tr>
            <td>{{ $item->description }} @if($item->unit)<br><small style="color:#9ca3af;">per {{ $item->unit }}</small>@endif</td>
            <td class="qty">{{ $item->quantity }}</td>
            <td class="amount">${{ number_format($item->unit_price, 2) }}</td>
            <td class="amount">${{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr><td>Subtotal</td><td>${{ number_format($quotation->subtotal, 2) }}</td></tr>
        @if($quotation->discount > 0)
        <tr><td>Discount</td><td>-${{ number_format($quotation->discount, 2) }}</td></tr>
        @endif
        @if($quotation->tax_rate > 0)
        <tr><td>Tax ({{ $quotation->tax_rate }}%)</td><td>${{ number_format($quotation->tax_amount, 2) }}</td></tr>
        @endif
        <tr class="grand-total"><td>Total</td><td>${{ number_format($quotation->total, 2) }}</td></tr>
    </table>
</div>

@if($quotation->notes)
<div class="notes">
    <h4>Notes</h4>
    <p>{{ $quotation->notes }}</p>
</div>
@endif

<div class="validity">
    <strong>Validity:</strong> This quotation is valid for 30 days from the date of issue. Pricing and availability are subject to change.
</div>

<div class="footer">
    Quotation {{ $quotation->number }} | Generated on {{ now()->format('M d, Y') }}
</div>

</body>
</html>
