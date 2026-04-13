<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'is_active',
    ];

    public function accounts()
    {
        return $this->hasMany(ClientAccount::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}