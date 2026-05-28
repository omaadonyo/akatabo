<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Balance</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 24px; color: #111827; }
        .details { margin-bottom: 24px; }
        .details p { margin: 4px 0; }
        .amounts { background: #f9fafb; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .amounts .row { display: flex; justify-content: space-between; padding: 4px 0; }
        .amounts .total { font-weight: bold; font-size: 18px; border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 8px; }
        .button { display: inline-block; padding: 10px 20px; background: #f59e0b; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; }
        .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invoice {{ $invoice->number }}</h1>
        </div>

        <div class="details">
            <p><strong>{{ $invoice->company->name }}</strong></p>
            <p>Date: {{ $invoice->date->format('F j, Y') }}</p>
            <p>Due Date: {{ $invoice->due_date->format('F j, Y') }}</p>
            <p>Status: {{ ucfirst($invoice->status) }}</p>
        </div>

        <div class="amounts">
            <div class="row">
                <span>Total Amount</span>
                <span>${{ number_format($invoice->total, 2) }}</span>
            </div>
            <div class="row">
                <span>Paid Amount</span>
                <span>${{ number_format($invoice->paid_amount, 2) }}</span>
            </div>
            <div class="row total">
                <span>Balance Due</span>
                <span>${{ number_format(max(0, $invoice->total - $invoice->paid_amount), 2) }}</span>
            </div>
        </div>

        <p>Thank you for your business.</p>
    </div>
</body>
</html>
