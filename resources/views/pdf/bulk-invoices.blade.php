<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoices Export</title>
<style>
    @page { margin: 15mm 10mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; color: #333; }
    h1 { font-size: 16pt; color: #d97706; border-bottom: 2px solid #d97706; padding-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #d97706; color: #fff; padding: 5px 6px; text-align: left; font-size: 7pt; text-transform: uppercase; }
    td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) td { background: #fffbeb; }
    .amount { text-align: right; }
    .page-break { page-break-after: always; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #9ca3af; font-size: 7pt; border-top: 1px solid #e5e7eb; padding-top: 5px; }
</style>
</head>
<body>
<h1>Invoices Export</h1>
<p style="font-size:8pt;color:#6b7280;">Generated {{ now()->format('M d, Y H:i') }} | {{ count($records) }} invoices</p>
<table>
<thead>
<tr>
    <th>Number</th>
    <th>Company</th>
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
    <td>{{ $invoice->number }}</td>
    <td>{{ $invoice->company?->name ?? '—' }}</td>
    <td>{{ $invoice->date?->format('Y-m-d') ?? '—' }}</td>
    <td>{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</td>
    <td class="amount">${{ number_format($invoice->total, 2) }}</td>
    <td class="amount">${{ number_format($invoice->paid_amount, 2) }}</td>
    <td>{{ ucfirst($invoice->status) }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">Invoices Export | Generated {{ now()->format('M d, Y') }}</div>
</body>
</html>
