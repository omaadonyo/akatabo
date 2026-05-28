<?php

namespace App\Models;

use App\Mail\QuotationMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
        static::saved(function ($quotation) {
            if ($quotation->isDirty('status') && $quotation->status === 'accepted') {
                $company = $quotation->company;
                if ($company && $company->email) {
                    Mail::to($company->email)->send(new QuotationMail($quotation));
                }
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('public.quotation.show', $this->id);
    }
}
