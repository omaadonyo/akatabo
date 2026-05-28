<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Confirmation</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 24px; color: #059669; }
        .details { margin-bottom: 24px; }
        .details p { margin: 4px 0; }
        .amounts { background: #f0fdf4; border-radius: 8px; padding: 16px; margin-bottom: 24px; border: 1px solid #bbf7d0; }
        .amounts .row { display: flex; justify-content: space-between; padding: 4px 0; }
        .amounts .total { font-weight: bold; font-size: 18px; border-top: 2px solid #059669; padding-top: 8px; margin-top: 8px; color: #059669; }
        .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Received</h1>
        </div>

        <div class="details">
            <p><strong>{{ $receipt->company->name ?? '' }}</strong></p>
            <p>Receipt: {{ $receipt->number }}</p>
            @if($receipt->invoice)
            <p>Invoice: {{ $receipt->invoice->number }}</p>
            @endif
            <p>Date: {{ $receipt->date->format('F j, Y') }}</p>
        </div>

        <div class="amounts">
            <div class="row">
                <span>Amount Received</span>
                <span>${{ number_format($receipt->total, 2) }}</span>
            </div>
            @if($receipt->invoice)
            <div class="row total">
                <span>Balance Remaining</span>
                <span>${{ number_format(max(0, $receipt->invoice->total - $receipt->invoice->paid_amount), 2) }}</span>
            </div>
            @endif
        </div>

        <p>Thank you for your payment. Please find the official receipt attached.</p>
    </div>
</body>
</html>
