<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'logo',
        'name',
        'email',
        'active',
        'address',
        'invoice_notes',
        'quotation_notes',
        'receipt_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
