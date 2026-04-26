<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BailmentAccount extends Model
{
    protected $fillable = [
        'client_id',
        'currency_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:4',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}