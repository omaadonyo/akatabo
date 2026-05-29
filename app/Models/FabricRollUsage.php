<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FabricRollUsage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fabric_roll_id',
        'company_id',
        'customer_id',
        'invoice_id',
        'meters_used',
        'remaining_before',
        'remaining_after',
        'date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'meters_used' => 'decimal:2',
            'remaining_before' => 'decimal:2',
            'remaining_after' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function fabricRoll(): BelongsTo
    {
        return $this->belongsTo(FabricRoll::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function booted()
    {
        static::created(function (self $usage) {
            $usage->fabricRoll->decrement('remaining_meters', $usage->meters_used);
            $usage->fabricRoll->updateStatus();
        });

        static::updated(function (self $usage) {
            if ($usage->isDirty('meters_used')) {
                $original = (float) $usage->getOriginal('meters_used');
                $current = (float) $usage->meters_used;
                $diff = $current - $original;

                if ($diff > 0) {
                    $usage->fabricRoll->decrement('remaining_meters', $diff);
                } elseif ($diff < 0) {
                    $usage->fabricRoll->increment('remaining_meters', abs($diff));
                }

                $usage->fabricRoll->updateStatus();
            }
        });

        static::deleted(function (self $usage) {
            if (! $usage->isForceDeleting()) {
                $usage->fabricRoll->increment('remaining_meters', $usage->meters_used);
                $usage->fabricRoll->updateStatus();
            }
        });

        static::restored(function (self $usage) {
            $usage->fabricRoll->decrement('remaining_meters', $usage->meters_used);
            $usage->fabricRoll->updateStatus();
        });
    }
}
