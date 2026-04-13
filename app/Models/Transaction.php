<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'branch_id',
        'currency_id',
        'to_currency_id',
        'type',
        'amount',
        'to_amount',
        'exchange_rate',
        'balance_before',
        'balance_after',
        'reference_no',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'to_amount' => 'decimal:4',
        'exchange_rate' => 'decimal:6',
        'balance_before' => 'decimal:4',
        'balance_after' => 'decimal:4',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}