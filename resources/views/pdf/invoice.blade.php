<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice {{ $invoice->number }}</title>
<style>
    @page { margin: 18mm 14mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #374151; line-height: 1.6; }
    .header-bar { background: #1e40af; margin: -18mm -14mm 18mm; padding: 22mm 14mm 14mm; color: #fff; }
    .header-bar h1 { font-size: 20pt; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
    .header-bar .number { font-size: 10pt; opacity: 0.7; font-family: 'Courier New', monospace; margin-top: 2px; }
    .header-bar .badge { float: right; padding: 4px 14px; border-radius: 20px; font-size: 8pt; font-weight: 700; text-transform: uppercase; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); }
    .section-grid { display: flex; gap: 24px; margin-bottom: 20px; }
    .section-grid > div { flex: 1; }
    .section-label { font-size: 7.5pt; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 4px; }
    .section-label strong { font-size: 9pt; color: #374151; font-weight: 600; }
    .section-label p { margin: 1px 0; font-size: 8.5pt; color: #6b7280; }
    .meta-bar { background: #eff6ff; border-radius: 6px; padding: 10px 16px; margin: 16px 0 20px; display: flex; gap: 32px; font-size: 8.5pt; }
    .meta-bar .label { color: #6b7280; }
    .meta-bar .value { font-weight: 600; color: #1e40af; }
    table.items { width: 100%; border-collapse: collapse; margin: 16px 0; }
    table.items th { background: #1e40af; color: #fff; padding: 8px 10px; text-align: left; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.06em; }
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
    .totals .grand td { font-size: 11pt; font-weight: 700; color: #1e40af; border-top: 2px solid #1e40af; padding-top: 8px; }
    .totals .paid td { color: #16a34a; }
    .totals .balance td { font-weight: 700; color: #dc2626; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    .notes-box { margin-top: 24px; padding: 12px 16px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #d1d5db; font-size: 8pt; color: #6b7280; }
    .notes-box h4 { margin: 0 0 4px; font-size: 8pt; color: #374151; }
    .payment-box { margin-top: 16px; padding: 12px 16px; background: #eff6ff; border-radius: 6px; border: 1px solid #bfdbfe; font-size: 8pt; color: #1e40af; }
    .payment-box h4 { margin: 0 0 4px; font-size: 8pt; color: #1e40af; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #d1d5db; font-size: 7pt; border-top: 1px solid #f3f4f6; padding-top: 6px; }
</style>
</head>
<body>

<div class="header-bar">
    <span class="badge">{{ ucfirst($invoice->status) }}</span>
    <h1>INVOICE</h1>
    <div class="number">{{ $invoice->number }}</div>
</div>

<div class="section-grid">
    <div>
        <div class="section-label">From</div>
        @if($invoice->company)
            <p><strong>{{ $invoice->company->name }}</strong></p>
            @if($invoice->company->address)<p>{{ $invoice->company->address }}</p>@endif
            @if($invoice->company->email)<p>{{ $invoice->company->email }}</p>@endif
        @endif
    </div>
    <div>
        <div class="section-label">Bill To</div>
        @if($invoice->customer)
            <p><strong>{{ $invoice->customer->name }}</strong></p>
            @if($invoice->customer->address)<p>{{ $invoice->customer->address }}</p>@endif
            @if($invoice->customer->email)<p>{{ $invoice->customer->email }}</p>@endif
        @elseif($invoice->company)
            <p><strong>{{ $invoice->company->name }}</strong></p>
        @endif
    </div>
</div>

<div class="meta-bar">
    <div><span class="label">Invoice Date</span><br><span class="value">{{ $invoice->date->format('M d, Y') }}</span></div>
    <div><span class="label">Due Date</span><br><span class="value">{{ $invoice->due_date->format('M d, Y') }}</span></div>
    <div><span class="label">Balance Due</span><br><span class="value">UGX {{ number_format($invoice->balance, 2) }}</span></div>
</div>

<table class="items">
    <tr>
        <th style="width:50%">Description</th>
        <th style="width:8%">Qty</th>
        <th style="width:20%">Price</th>
        <th style="width:22%">Amount</th>
    </tr>
    @foreach($invoice->items as $item)
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
        <tr class="sub"><td>Subtotal</td><td>UGX {{ number_format($invoice->subtotal, 2) }}</td></tr>
        @if($invoice->discount > 0)
        <tr class="sub"><td>Discount</td><td>-UGX {{ number_format($invoice->discount, 2) }}</td></tr>
        @endif
        @if($invoice->tax_rate > 0)
        <tr class="sub"><td>Tax ({{ $invoice->tax_rate }}%)</td><td>UGX {{ number_format($invoice->tax_amount, 2) }}</td></tr>
        @endif
        <tr class="grand"><td>Total</td><td>UGX {{ number_format($invoice->total, 2) }}</td></tr>
        @if($invoice->paid_amount > 0)
        <tr class="paid"><td>Paid</td><td>-UGX {{ number_format($invoice->paid_amount, 2) }}</td></tr>
        <tr class="balance"><td>Balance Due</td><td>UGX {{ number_format($invoice->balance, 2) }}</td></tr>
        @endif
    </table>
</div>

@if($invoice->company?->invoice_notes || isset($qrPath))
<table style="width:100%; margin-top: 20px;"><tr>
    @if($invoice->company?->invoice_notes)
    <td style="width:65%; vertical-align:top; padding: 12px 16px; background: #f9fafb; border-radius:6px; border-left:3px solid #d1d5db;">
        <h4 style="margin: 0 0 4px; font-size: 8pt; color: #374151;">Notes</h4>
        <p style="margin:0; font-size:8pt; color:#6b7280;">{{ $invoice->company->invoice_notes }}</p>
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

@if($invoice->notes)
<div class="notes-box">
    <h4>Document Notes</h4>
    <p>{{ $invoice->notes }}</p>
</div>
@endif

<div class="payment-box">
    <h4>Payment Information</h4>
    <p>Please make payment to the account details provided on your invoice statement.</p>
    <p>Invoice Reference: <strong>{{ $invoice->number }}</strong></p>
</div>

<div class="footer">
    Invoice {{ $invoice->number }} | Generated {{ now()->format('M d, Y') }}
</div>

</body>
</html>
