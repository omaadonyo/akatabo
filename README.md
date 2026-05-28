# Akatabo — Financial Document Management System

A full-featured invoicing and financial document management system built with **Laravel 13**, **Filament 5**, and **Alpine.js**. Manage quotations, invoices, receipts, inventory, and transactions with PDF exports, email notifications, QR codes, and dashboard analytics.

## Features

- **Quotations** — Create, preview, and manage quotations with auto-numbering (`QOT-2026-XXXX`)
- **Invoices** — Generate invoices from quotations, track payments, auto-status updates
- **Receipts** — Record full or partial payments, auto-sync paid amounts to invoices
- **Transactions** — Unified audit log auto-created on every document event
- **Inventory** — Product/service catalog with stock tracking, low-stock alerts, and images
- **PDF Exports** — Professional PDF templates with DomPDF and QR codes (via `chillerlan/php-qrcode`)
- **Bulk Export** — CSV and PDF bulk export actions on Invoice, Receipt, and Transaction tables
- **Email Notifications** — Send invoice/receipt PDFs via email with configurable mail driver
- **Database Notifications** — Real-time alerts for document creation events
- **Dashboard** — Revenue charts, invoice status breakdowns, monthly trends, low-stock alerts
- **Public Pages** — Public invoice and receipt viewing with QR code scanning
- **Live Preview** — Alpine.js inline form previews for quotations, invoices, and receipts
- **AMBER Theme** — Consistent amber color scheme across all views and PDFs

## Requirements

- PHP 8.3+
- Composer 2.x
- Node.js 20+ and NPM
- MySQL / MariaDB / PostgreSQL
- GD Extension (for QR code PNG generation)
- `pcntl` extension (optional, for queue workers; not available on Windows)

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/omaadonyo/akatabo.git
cd akatabo

# 2. Install PHP dependencies
composer install

# 3. Install and build frontend assets
npm install
npm run build

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Configure your database in .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=akatabo
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Create storage link (for product images)
php artisan storage:link

# 8. Create an admin user
php artisan make:filament-user
#    Follow the prompts to set name, email, and password

# 9. Start the development server
php artisan serve
#    Visit http://localhost:8000/admin to access the panel
```

## Quick Start

1. **Add a Company** — Navigate to Companies and create a company profile (required for documents)
2. **Add Products** — Set up your product/service catalog under Inventory
3. **Create a Quotation** — Fill in the form; items, totals, and preview update in real-time
4. **Convert to Invoice** — Once a quotation is accepted, use the "Create Invoice" action
5. **Record Payment** — On the Invoice table, use "Record Payment" or "Create Full Receipt"
6. **Send Documents** — Use "Email Invoice" / "Email Receipt" actions to send PDFs
7. **Export** — Use bulk actions to export CSV or PDF reports

## Email Configuration

By default, the mail driver is set to `log` — emails are written to `storage/logs/laravel.log`. To send real emails, update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@email.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Project Structure

| Path | Description |
|------|-------------|
| `app/Models/` | Quotation, Invoice, Receipt, Product, Company, Transaction |
| `app/Filament/Resources/` | Filament resources organized by feature (Quotation, Invoice, Receipt, Inventory, Companies, Transaction) |
| `app/Filament/Actions/` | Reusable bulk export actions (CSV, PDF) |
| `app/Filament/Pages/` | Custom dashboard |
| `app/Filament/Widgets/` | Stats widgets for dashboard and list pages |
| `app/Helpers/QrCodeHelper.php` | QR code generation (SVG for web, PNG files for DomPDF) |
| `app/Mail/` | Mailable classes with PDF attachments |
| `app/Notifications/` | Database notifications for document events |
| `resources/views/pdf/` | PDF templates (invoice, quotation, receipt, bulk exports) |
| `resources/views/filament/` | Custom slide-over views and form previews |
| `resources/views/public/` | Public-facing document pages |
| `resources/views/emails/` | Email body templates |
| `routes/web.php` | Public routes for invoice/receipt viewing |

## Key Decisions

- **Inline styles** for custom Blade views — avoids Tailwind v4 CSS conflicts with Filament's built-in styles
- **Model events** for `paid_amount` sync — Receipt created/updated/deleted events auto-update Invoice balance
- **`Action::make('view')` + `slideOver()`** instead of `ViewAction` — guarantees slide-over modal behavior
- **Single Product model** with `type` discriminator (`product`/`service`) — simpler than separate tables
- **`chillerlan/php-qrcode`** — works with PHP 8.3 (unlike `endroid/qr-code` which needs 8.4)

## License

[MIT](LICENSE)
