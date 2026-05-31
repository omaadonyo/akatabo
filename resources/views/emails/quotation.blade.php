<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->number }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif; }
        .outer { max-width: 600px; margin: 0 auto; padding: 24px 16px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .card-header { background: linear-gradient(135deg, #7c3aed, #a855f7); padding: 32px 32px 24px; color: #fff; }
        .card-header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.03em; }
        .card-header p { margin: 4px 0 0; opacity: 0.75; font-size: 13px; }
        .card-body { padding: 28px 32px; }
        .details { margin-bottom: 20px; }
        .details p { margin: 4px 0; font-size: 14px; color: #6b7280; }
        .details strong { color: #1f2937; }
        .accepted-badge { display: inline-block; padding: 4px 16px; background: #d1fae5; color: #16a34a; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .amount-box { background: #f5f3ff; border-radius: 10px; padding: 20px; margin: 20px 0; border: 1px solid #ddd6fe; }
        .amount-box .row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 14px; color: #6b7280; }
        .amount-box .total { font-weight: 700; font-size: 18px; color: #7c3aed; border-top: 2px solid #ddd6fe; padding-top: 10px; margin-top: 6px; }
        .footer-text { margin-top: 28px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="outer">
        <div class="card">
            <div class="card-header">
                <h1>Quotation {{ $quotation->number }}</h1>
                <p>{{ $quotation->company->name ?? '' }}</p>
            </div>
            <div class="card-body">
                <div class="details">
                    <p><strong>Customer:</strong> {{ $quotation->customer->name ?? $quotation->company->name ?? '' }}</p>
                    <p><strong>Date:</strong> {{ $quotation->date->format('F j, Y') }}</p>
                    <p><strong>Status:</strong> <span style="color:#7c3aed;font-weight:600;">{{ ucfirst($quotation->status) }}</span></p>
                    @if($quotation->status === 'accepted')
                    <p style="margin-top:8px;"><span class="accepted-badge">ACCEPTED</span></p>
                    @endif
                </div>

                <div class="amount-box">
                    <div class="row">
                        <span>Subtotal</span>
                        <span>UGX {{ number_format($quotation->subtotal, 2) }}</span>
                    </div>
                    @if ($quotation->discount > 0)
                    <div class="row">
                        <span>Discount</span>
                        <span>-UGX {{ number_format($quotation->discount, 2) }}</span>
                    </div>
                    @endif
                    @if ($quotation->tax_rate > 0)
                    <div class="row">
                        <span>Tax ({{ $quotation->tax_rate }}%)</span>
                        <span>UGX {{ number_format($quotation->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="row total">
                        <span>Total</span>
                        <span>UGX {{ number_format($quotation->total, 2) }}</span>
                    </div>
                </div>

                <p style="font-size:14px;color:#6b7280;margin:0;">The quotation document is attached. This quotation is valid for 30 days.</p>
            </div>
            <div style="padding:0 32px 28px;text-align:center;">
                <div style="font-size:12px;color:#d1d5db;border-top:1px solid #f3f4f6;padding-top:16px;">{{ $quotation->company->name ?? 'Company' }} &mdash; {{ $quotation->company->email ?? '' }}</div>
            </div>
        </div>
        <div style="text-align:center;padding:16px 0 0;font-size:11px;color:#d1d5db;">Generated on {{ now()->format('F j, Y') }}</div>
    </div>
</body>
</html>
