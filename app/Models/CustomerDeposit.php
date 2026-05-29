<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDeposit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'amount',
        'type',
        'reference_type',
        'reference_id',
        'notes',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    protected static function booted()
    {
        static::created(function ($deposit) {
            $amount = (float) $deposit->amount;
            if (in_array($deposit->type, ['deposit'])) {
                $deposit->customer->increment('deposit_balance', $amount);
            } else {
                $deposit->customer->decrement('deposit_balance', $amount);
            }
        });

        static::updated(function ($deposit) {
            if ($deposit->isDirty('amount') || $deposit->isDirty('type')) {
                $deposit->load('customer');
                $deposit->customer->update(['deposit_balance' => $deposit->customer->deposits()
                    ->withTrashed()
                    ->get()
                    ->sum(function ($d) {
                        $amount = (float) $d->amount;
                        return in_array($d->type, ['deposit']) ? $amount : -$amount;
                    }),
                ]);
            }
        });

        static::deleted(function ($deposit) {
            $amount = (float) $deposit->amount;
            if (in_array($deposit->type, ['deposit'])) {
                $deposit->customer->decrement('deposit_balance', $amount);
            } else {
                $deposit->customer->increment('deposit_balance', $amount);
            }
        });

        static::restored(function ($deposit) {
            $amount = (float) $deposit->amount;
            if (in_array($deposit->type, ['deposit'])) {
                $deposit->customer->increment('deposit_balance', $amount);
            } else {
                $deposit->customer->decrement('deposit_balance', $amount);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
