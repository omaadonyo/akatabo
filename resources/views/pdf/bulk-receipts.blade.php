<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Receipts Export</title>
<style>
    @page { margin: 15mm 10mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; color: #333; }
    h1 { font-size: 16pt; color: #d97706; border-bottom: 2px solid #d97706; padding-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #d97706; color: #fff; padding: 5px 6px; text-align: left; font-size: 7pt; text-transform: uppercase; }
    td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) td { background: #fffbeb; }
    .amount { text-align: right; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #9ca3af; font-size: 7pt; border-top: 1px solid #e5e7eb; padding-top: 5px; }
</style>
</head>
<body>
<h1>Receipts Export</h1>
<p style="font-size:8pt;color:#6b7280;">Generated {{ now()->format('M d, Y H:i') }} | {{ count($records) }} receipts</p>
<table>
<thead>
<tr>
    <th>Number</th>
    <th>Invoice</th>
    <th>Company</th>
    <th>Date</th>
    <th class="amount">Total</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
@foreach($records as $receipt)
<tr>
    <td>{{ $receipt->number }}</td>
    <td>{{ $receipt->invoice?->number ?? '—' }}</td>
    <td>{{ $receipt->company?->name ?? '—' }}</td>
    <td>{{ $receipt->date?->format('Y-m-d') ?? '—' }}</td>
    <td class="amount">${{ number_format($receipt->total, 2) }}</td>
    <td>{{ ucfirst($receipt->status) }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">Receipts Export | Generated {{ now()->format('M d, Y') }}</div>
</body>
</html>
