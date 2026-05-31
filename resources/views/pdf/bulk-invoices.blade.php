<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoices Export</title>
<style>
    @page { margin: 12mm 10mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; color: #374151; }
    h1 { font-size: 15pt; font-weight: 800; color: #1e40af; margin: 0 0 2px; }
    .subtitle { font-size: 7.5pt; color: #9ca3af; margin: 0 0 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th { background: #1e40af; color: #fff; padding: 6px 8px; text-align: left; font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 7.5pt; }
    tr:nth-child(even) td { background: #fafafa; }
    .amount { text-align: right; font-weight: 500; }
    .page-break { page-break-after: always; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #d1d5db; font-size: 6.5pt; border-top: 1px solid #f3f4f6; padding-top: 4px; }
</style>
</head>
<body>
<h1>Invoices Export</h1>
<p class="subtitle">Generated {{ now()->format('M d, Y H:i') }} | {{ count($records) }} invoices</p>
<table>
<thead>
<tr>
    <th>Number</th>
    <th>Customer</th>
    <th>Date</th>
    <th>Due Date</th>
    <th class="amount">Total</th>
    <th class="amount">Paid</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
@foreach($records as $invoice)
<tr>
    <td style="font-family: monospace;">{{ $invoice->number }}</td>
    <td>{{ $invoice->customer?->name ?? $invoice->company?->name ?? '—' }}</td>
    <td>{{ $invoice->date?->format('Y-m-d') ?? '—' }}</td>
    <td>{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</td>
    <td class="amount">UGX {{ number_format($invoice->total, 2) }}</td>
    <td class="amount">{{ $invoice->paid_amount > 0 ? 'UGX '.number_format($invoice->paid_amount, 2) : '—' }}</td>
    <td>{{ ucfirst($invoice->status) }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">Invoices Export | Generated {{ now()->format('M d, Y') }}</div>
</body>
</html>
