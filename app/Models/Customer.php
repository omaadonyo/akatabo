<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'email',
        'deposit_balance',
    ];

    protected function casts(): array
    {
        return [
            'deposit_balance' => 'decimal:2',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function deposits()
    {
        return $this->hasMany(CustomerDeposit::class);
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->invoices()
            ->whereNotIn('status', ['draft', 'cancelled', 'paid'])
            ->get()
            ->sum(fn ($inv) => $inv->balance);
    }
}
