<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $receipt->number }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif; }
        .outer { max-width: 600px; margin: 0 auto; padding: 24px 16px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .card-header { background: linear-gradient(135deg, #047857, #10b981); padding: 32px 32px 24px; color: #fff; }
        .card-header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.03em; }
        .card-header p { margin: 4px 0 0; opacity: 0.75; font-size: 13px; }
        .card-body { padding: 28px 32px; }
        .details { margin-bottom: 20px; }
        .details p { margin: 4px 0; font-size: 14px; color: #6b7280; }
        .details strong { color: #1f2937; }
        .paid-badge { display: inline-block; padding: 4px 16px; background: #d1fae5; color: #047857; border-radius: 20px; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; }
        .amount-box { background: #f0fdf4; border-radius: 10px; padding: 20px; margin: 20px 0; border: 1px solid #bbf7d0; }
        .amount-box .row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 14px; color: #6b7280; }
        .amount-box .total { font-weight: 700; font-size: 18px; color: #047857; border-top: 2px solid #bbf7d0; padding-top: 10px; margin-top: 6px; }
        .footer-text { margin-top: 28px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="outer">
        <div class="card">
            <div class="card-header">
                <h1>Payment Received</h1>
                <p>{{ $receipt->company->name ?? '' }}</p>
            </div>
            <div class="card-body">
                <div class="details">
                    <p><strong>Customer:</strong> {{ $receipt->customer->name ?? $receipt->company->name ?? '' }}</p>
                    <p><strong>Receipt:</strong> {{ $receipt->number }}</p>
                    @if($receipt->invoice)<p><strong>Invoice:</strong> {{ $receipt->invoice->number }}</p>@endif
                    <p><strong>Date:</strong> {{ $receipt->date->format('F j, Y') }}</p>
                    <p style="margin-top:8px;"><span class="paid-badge">PAID</span></p>
                </div>

                <div class="amount-box">
                    <div class="row">
                        <span>Amount Received</span>
                        <span>UGX {{ number_format($receipt->total, 2) }}</span>
                    </div>
                    @if($receipt->invoice)
                    <div class="row total">
                        <span>Balance Remaining</span>
                        <span>UGX {{ number_format(max(0, $receipt->invoice->total - $receipt->invoice->paid_amount), 2) }}</span>
                    </div>
                    @endif
                </div>

                <p style="font-size:14px;color:#6b7280;margin:0;">Thank you for your payment. The official receipt is attached.</p>
            </div>
            <div style="padding:0 32px 28px;text-align:center;">
                <div style="font-size:12px;color:#d1d5db;border-top:1px solid #f3f4f6;padding-top:16px;">{{ $receipt->company->name ?? 'Company' }} &mdash; {{ $receipt->company->email ?? '' }}</div>
            </div>
        </div>
        <div style="text-align:center;padding:16px 0 0;font-size:11px;color:#d1d5db;">Generated on {{ now()->format('F j, Y') }}</div>
    </div>
</body>
</html>
