<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Transactions Export</title>
<style>
    @page { margin: 12mm 10mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; color: #374151; }
    h1 { font-size: 15pt; font-weight: 800; color: #d97706; margin: 0 0 2px; }
    .subtitle { font-size: 7.5pt; color: #9ca3af; margin: 0 0 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th { background: #d97706; color: #fff; padding: 6px 8px; text-align: left; font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 7.5pt; }
    tr:nth-child(even) td { background: #fafafa; }
    .amount { text-align: right; font-weight: 500; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #d1d5db; font-size: 6.5pt; border-top: 1px solid #f3f4f6; padding-top: 4px; }
</style>
</head>
<body>
<h1>Transactions Export</h1>
<p class="subtitle">Generated {{ now()->format('M d, Y H:i') }} | {{ count($records) }} transactions</p>
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
    <td style="font-family: monospace;">{{ $txn->document_number ?? '—' }}</td>
    <td>{{ $txn->company?->name ?? '—' }}</td>
    <td class="amount">UGX {{ number_format($txn->amount, 2) }}</td>
    <td>{{ ucfirst($txn->status) }}</td>
    <td>{{ $txn->description ?? '—' }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">Transactions Export | Generated {{ now()->format('M d, Y') }}</div>
</body>
</html>
