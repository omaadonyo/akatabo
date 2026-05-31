<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Receipts Export</title>
<style>
    @page { margin: 12mm 10mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; color: #374151; }
    h1 { font-size: 15pt; font-weight: 800; color: #047857; margin: 0 0 2px; }
    .subtitle { font-size: 7.5pt; color: #9ca3af; margin: 0 0 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th { background: #047857; color: #fff; padding: 6px 8px; text-align: left; font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 7.5pt; }
    tr:nth-child(even) td { background: #fafafa; }
    .amount { text-align: right; font-weight: 500; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #d1d5db; font-size: 6.5pt; border-top: 1px solid #f3f4f6; padding-top: 4px; }
</style>
</head>
<body>
<h1>Receipts Export</h1>
<p class="subtitle">Generated {{ now()->format('M d, Y H:i') }} | {{ count($records) }} receipts</p>
<table>
<thead>
<tr>
    <th>Number</th>
    <th>Invoice</th>
    <th>Customer</th>
    <th>Date</th>
    <th class="amount">Total</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
@foreach($records as $receipt)
<tr>
    <td style="font-family: monospace;">{{ $receipt->number }}</td>
    <td>{{ $receipt->invoice?->number ?? '—' }}</td>
    <td>{{ $receipt->customer?->name ?? $receipt->company?->name ?? '—' }}</td>
    <td>{{ $receipt->date?->format('Y-m-d') ?? '—' }}</td>
    <td class="amount">UGX {{ number_format($receipt->total, 2) }}</td>
    <td>{{ ucfirst($receipt->status) }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">Receipts Export | Generated {{ now()->format('M d, Y') }}</div>
</body>
</html>
