<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'project_id',
        'company_id',
        'customer_id',
        'user_id',
        'number',
        'date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'paid_amount',
        'status',
        'notes',
    ];

    protected $appends = ['balance'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function getBalanceAttribute()
    {
        return max(0, ($this->total ?? 0) - ($this->paid_amount ?? 0));
    }

    protected static function booted()
    {
        static::saved(function ($invoice) {
            if ($invoice->isDirty('status')) {
                $original = $invoice->getOriginal('status');
                $current = $invoice->status;

                $deductStatuses = ['sent', 'paid'];
                $restoreStatuses = ['draft', 'cancelled'];

                $shouldDeduct = in_array($current, $deductStatuses) && !in_array($original, $deductStatuses);
                $shouldRestore = in_array($current, $restoreStatuses) && in_array($original, $deductStatuses);

                if ($shouldDeduct || $shouldRestore) {
                    $invoice->load('items.product', 'items.fabricRoll');
                    foreach ($invoice->items as $item) {
                        if ($item->product && !$item->product->isService() && $item->product->stock_quantity !== null) {
                            if ($shouldDeduct) {
                                $item->product->decrement('stock_quantity', $item->quantity);
                            } else {
                                $item->product->increment('stock_quantity', $item->quantity);
                            }
                        }
                        if ($item->fabricRoll) {
                            if ($shouldDeduct) {
                                $item->fabricRoll->decrement('remaining_meters', $item->quantity);
                                $item->fabricRoll->updateStatus();
                            } else {
                                $item->fabricRoll->increment('remaining_meters', $item->quantity);
                                $item->fabricRoll->updateStatus();
                            }
                        }
                    }
                }
            }
        });
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
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
        return $this->hasMany(InvoiceItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('public.invoice.show', $this->id);
    }
}
