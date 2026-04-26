<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BailmentTransaction extends Model
{
    protected $fillable = [
        'client_id',
        'branch_id',
        'currency_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'sender_name',
        'sender_phone',
        'notes',
        'reference_no',
        'receipt_number',
        'created_by',
        'seen_by_client',
        'seen_at',
    ];

    protected $casts = [
        'amount'         => 'decimal:4',
        'balance_before' => 'decimal:4',
        'balance_after'  => 'decimal:4',
        'seen_by_client' => 'boolean',
        'seen_at'        => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
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