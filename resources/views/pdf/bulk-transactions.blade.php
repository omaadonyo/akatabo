<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Transactions Export</title>
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
<h1>Transactions Export</h1>
<p style="font-size:8pt;color:#6b7280;">Generated {{ now()->format('M d, Y H:i') }} | {{ count($records) }} transactions</p>
<table>
<thead>
<tr>
    <th>Date</th>
    <th>Type</th>
    <th>Document</th>
    <th>Company</th>
    <th class="amount">Amount</th>
    <th>Status</th>
    <th>Description</th>
</tr>
</thead>
<tbody>
@foreach($records as $txn)
<tr>
    <td>{{ $txn->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
    <td>{{ ucfirst($txn->type) }}</td>
    <td>{{ $txn->document_number ?? '—' }}</td>
    <td>{{ $txn->company?->name ?? '—' }}</td>
    <td class="amount">${{ number_format($txn->amount, 2) }}</td>
    <td>{{ ucfirst($txn->status) }}</td>
    <td>{{ $txn->description ?? '—' }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">Transactions Export | Generated {{ now()->format('M d, Y') }}</div>
</body>
</html>
