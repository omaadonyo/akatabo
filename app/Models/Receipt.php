<?php

namespace App\Models;

use App\Notifications\ReceiptAddedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'project_id',
        'company_id',
        'customer_id',
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
        static::created(function ($receipt) {
            if ($receipt->invoice && $receipt->status === 'issued') {
                $receipt->invoice->increment('paid_amount', $receipt->total);
            }

            if ($receipt->user_id) {
                $user = \App\Models\User::find($receipt->user_id);
                $user?->notify(new ReceiptAddedNotification($receipt));
            }
        });

        static::updated(function ($receipt) {
            if ($receipt->isDirty('status') || $receipt->isDirty('total')) {
                $totalPaid = static::where('invoice_id', $receipt->invoice_id)
                    ->where('status', 'issued')
                    ->sum('total');

                if ($receipt->invoice) {
                    $receipt->invoice->update(['paid_amount' => $totalPaid]);
                }
            }
        });

        static::deleted(function ($receipt) {
            if ($receipt->invoice && $receipt->status === 'issued') {
                $receipt->invoice->decrement('paid_amount', $receipt->total);
            }
        });

        static::restored(function ($receipt) {
            if ($receipt->invoice && $receipt->status === 'issued') {
                $receipt->invoice->increment('paid_amount', $receipt->total);
            }
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
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
        return $this->hasMany(ReceiptItem::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('public.receipt.show', $this->id);
    }
}
