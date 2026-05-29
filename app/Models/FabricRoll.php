<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FabricRoll extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'roll_code',
        'company_id',
        'fabric_name',
        'color',
        'supplier',
        'date_received',
        'claimed_meters',
        'verified_meters',
        'buying_price_per_meter',
        'selling_price_per_meter',
        'fabric_width',
        'status',
        'remaining_meters',
        'notes',
    ];

    public function casts(): array
    {
        return [
            'claimed_meters' => 'decimal:2',
            'verified_meters' => 'decimal:2',
            'buying_price_per_meter' => 'decimal:2',
            'selling_price_per_meter' => 'decimal:2',
            'fabric_width' => 'decimal:2',
            'remaining_meters' => 'decimal:2',
            'date_received' => 'date',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'fabric_roll_id');
    }

    public static function booted()
    {
        static::created(function ($roll) {
            $roll->createLinkedProduct();
        });

        static::updated(function ($roll) {
            if ($roll->isDirty(['fabric_name', 'color', 'selling_price_per_meter'])) {
                $product = $roll->product;
                if ($product) {
                    $product->update([
                        'name' => $roll->fabric_name . ' - ' . $roll->color,
                        'unit_price' => $roll->selling_price_per_meter,
                    ]);
                }
            }
        });

        static::deleted(function ($roll) {
            if ($roll->product) {
                $roll->product->delete();
            }
        });

        static::restored(function ($roll) {
            if ($roll->product()->withTrashed()->first()) {
                $roll->product()->withTrashed()->first()->restore();
            }
        });
    }

    protected function createLinkedProduct()
    {
        $this->product()->create([
            'company_id' => $this->company_id,
            'name' => $this->fabric_name . ' - ' . $this->color,
            'sku' => $this->roll_code,
            'type' => 'product',
            'unit' => 'm',
            'unit_price' => $this->selling_price_per_meter,
            'buying_price' => $this->buying_price_per_meter,
            'stock_quantity' => $this->remaining_meters,
            'low_stock_threshold' => $this->verified_meters * 0.1,
            'description' => $this->fabric_name . ' - ' . $this->color . ' (' . $this->supplier . ') [' . $this->roll_code . ']',
            'is_active' => true,
        ]);
    }

    public function getRemainingPercentageAttribute(): float|int
    {
        if ($this->verified_meters <= 0) {
            return 0;
        }
        return ($this->remaining_meters / $this->verified_meters) * 100;
    }

    public function getUsedMetersAttribute(): float|int
    {
        return $this->verified_meters - $this->remaining_meters;
    }

    public function isLowStock(): bool
    {
        return $this->remaining_meters <= ($this->verified_meters * 0.1);
    }

    public function usages()
    {
        return $this->hasMany(FabricRollUsage::class);
    }

    public function updateStatus(): void
    {
        $newStatus = match (true) {
            $this->remaining_meters <= 0 => 'depleted',
            $this->remaining_meters < $this->verified_meters => 'partially_used',
            default => 'in_stock',
        };

        if ($this->status !== $newStatus) {
            $this->updateQuietly(['status' => $newStatus]);
        }
    }

    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    public function scopeLowStock($query)
    {
        return $query->where('remaining_meters', '>', 0)
            ->whereRaw('remaining_meters <= verified_meters * 0.1');
    }
}
