<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'rate',
        'rate_date',
        'set_by',
        'branch_id',
    ];

    protected $casts = [
        'rate_date' => 'date',
        'rate' => 'decimal:6',
    ];

    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}