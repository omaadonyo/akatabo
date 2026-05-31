<?php

namespace App\Models;

use App\Mail\InvoiceMail;
use App\Mail\QuotationMail;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'project_id',
        'user_id',
        'number',
        'date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::saved(function ($quotation) {
            if ($quotation->isDirty('status') && $quotation->status === 'accepted') {
                $company = $quotation->company;

                if (!Invoice::where('quotation_id', $quotation->id)->exists()) {
                    $lastInvoice = Invoice::withTrashed()
                        ->where('number', 'like', 'INV-' . date('Y') . '-%')
                        ->orderBy('number', 'desc')
                        ->first();

                    if ($lastInvoice) {
                        $lastNumber = (int) substr($lastInvoice->number, -4);
                        $number = 'INV-' . date('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                    } else {
                        $number = 'INV-' . date('Y') . '-0001';
                    }

                    $invoice = Invoice::create([
                        'quotation_id' => $quotation->id,
                        'company_id' => $quotation->company_id,
                        'customer_id' => $quotation->customer_id,
                        'project_id' => $quotation->project_id,
                        'user_id' => $quotation->user_id,
                        'number' => $number,
                        'date' => now(),
                        'due_date' => now()->addDays(30),
                        'subtotal' => $quotation->subtotal,
                        'tax_rate' => $quotation->tax_rate,
                        'tax_amount' => $quotation->tax_amount,
                        'discount' => $quotation->discount,
                        'total' => $quotation->total,
                        'status' => 'sent',
                        'notes' => $quotation->notes,
                    ]);

                    $quotation->load('items');
                    foreach ($quotation->items as $item) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'product_id' => $item->product_id,
                            'fabric_roll_id' => $item->fabric_roll_id,
                            'description' => $item->description,
                            'unit' => $item->unit,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'amount' => $item->amount,
                        ]);
                    }

                    $invoice->deductStockAndRecordUsage();

                    Transaction::create([
                        'company_id' => $invoice->company_id,
                        'user_id' => $invoice->user_id,
                        'type' => 'invoice',
                        'document_number' => $invoice->number,
                        'document_id' => $invoice->id,
                        'document_type' => Invoice::class,
                        'amount' => $invoice->total,
                        'date' => $invoice->date,
                        'status' => $invoice->status,
                        'description' => 'Invoice ' . $invoice->number,
                    ]);

                    if ($company && $company->email) {
                        Mail::to($company->email)->send(new InvoiceMail($invoice));
                    }
                }

                if ($company && $company->email) {
                    Mail::to($company->email)->send(new QuotationMail($quotation));
                }
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('public.quotation.show', $this->id);
    }
}
