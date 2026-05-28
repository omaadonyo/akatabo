<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'type',
        'description',
        'image',
        'unit',
        'unit_price',
        'currency',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
    ];

    public function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'stock_quantity' => 'decimal:2',
            'low_stock_threshold' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isService(): bool
    {
        return $this->type === 'service';
    }

    public function isLowStock(): bool
    {
        if ($this->isService() || $this->stock_quantity === null || $this->low_stock_threshold === null) {
            return false;
        }
        return $this->stock_quantity <= $this->low_stock_threshold;
    }
}
