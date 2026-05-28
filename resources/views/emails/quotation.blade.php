<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Accepted</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 24px; color: #d97706; }
        .details { margin-bottom: 24px; }
        .details p { margin: 4px 0; }
        .amounts { background: #fffbeb; border-radius: 8px; padding: 16px; margin-bottom: 24px; border: 1px solid #fde68a; }
        .amounts .row { display: flex; justify-content: space-between; padding: 4px 0; }
        .amounts .total { font-weight: bold; font-size: 18px; border-top: 2px solid #d97706; padding-top: 8px; margin-top: 8px; color: #d97706; }
        .button { display: inline-block; padding: 10px 20px; background: #d97706; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; }
        .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quotation Accepted</h1>
        </div>

        <p>Your quotation has been accepted.</p>

        <div class="details">
            <p><strong>{{ $quotation->customer->name ?? $quotation->company->name ?? '' }}</strong></p>
            <p>Quotation: {{ $quotation->number }}</p>
            <p>Date: {{ $quotation->date->format('F j, Y') }}</p>
            <p>Status: {{ ucfirst($quotation->status) }}</p>
        </div>

        <div class="amounts">
            <div class="row">
                <span>Total Amount</span>
                <span>${{ number_format($quotation->total, 2) }}</span>
            </div>
        </div>

        <p>Please find the quotation details attached. You can also view it online by scanning the QR code on the PDF.</p>

        <p>Thank you for your business.</p>
    </div>
</body>
</html>
