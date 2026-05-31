<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif; }
        .outer { max-width: 600px; margin: 0 auto; padding: 24px 16px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .card-header { background: linear-gradient(135deg, #1e40af, #3b82f6); padding: 32px 32px 24px; color: #fff; }
        .card-header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.03em; }
        .card-header p { margin: 4px 0 0; opacity: 0.75; font-size: 13px; }
        .card-body { padding: 28px 32px; }
        .details { margin-bottom: 20px; }
        .details p { margin: 4px 0; font-size: 14px; color: #6b7280; }
        .details strong { color: #1f2937; }
        .amount-box { background: #eff6ff; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .amount-box .row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 14px; color: #6b7280; }
        .amount-box .total { font-weight: 700; font-size: 18px; color: #1e40af; border-top: 2px solid #bfdbfe; padding-top: 10px; margin-top: 6px; }
        .btn { display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #1e40af, #3b82f6); color: #fff; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; margin: 8px 0; }
        .footer-text { margin-top: 28px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="outer">
        <div class="card">
            <div class="card-header">
                <h1>Invoice {{ $invoice->number }}</h1>
                <p>{{ $invoice->company->name ?? '' }}</p>
            </div>
            <div class="card-body">
                <div class="details">
                    <p><strong>Customer:</strong> {{ $invoice->customer->name ?? $invoice->company->name ?? '' }}</p>
                    <p><strong>Date:</strong> {{ $invoice->date->format('F j, Y') }}</p>
                    <p><strong>Due Date:</strong> {{ $invoice->due_date->format('F j, Y') }}</p>
                    <p><strong>Status:</strong> <span style="color:#d97706;font-weight:600;">{{ ucfirst($invoice->status) }}</span></p>
                </div>

                <div class="amount-box">
                    <div class="row">
                        <span>Subtotal</span>
                        <span>UGX {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if ($invoice->discount > 0)
                    <div class="row">
                        <span>Discount</span>
                        <span>-UGX {{ number_format($invoice->discount, 2) }}</span>
                    </div>
                    @endif
                    @if ($invoice->tax_rate > 0)
                    <div class="row">
                        <span>Tax ({{ $invoice->tax_rate }}%)</span>
                        <span>UGX {{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    @if ($invoice->paid_amount > 0)
                    <div class="row" style="color:#16a34a;">
                        <span>Paid</span>
                        <span>-UGX {{ number_format($invoice->paid_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="row total">
                        <span>{{ $invoice->paid_amount > 0 ? 'Balance Due' : 'Total' }}</span>
                        <span>UGX {{ number_format($invoice->paid_amount > 0 ? $invoice->balance : $invoice->total, 2) }}</span>
                    </div>
                </div>

                <p style="font-size:14px;color:#6b7280;margin:0;">Please find the invoice document attached. Thank you for your business.</p>
            </div>
            <div style="padding:0 32px 28px;text-align:center;">
                <div style="font-size:12px;color:#d1d5db;border-top:1px solid #f3f4f6;padding-top:16px;">{{ $invoice->company->name ?? 'Company' }} &mdash; {{ $invoice->company->email ?? '' }}</div>
            </div>
        </div>
        <div style="text-align:center;padding:16px 0 0;font-size:11px;color:#d1d5db;">Generated on {{ now()->format('F j, Y') }}</div>
    </div>
</body>
</html>
