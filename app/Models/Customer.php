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
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
