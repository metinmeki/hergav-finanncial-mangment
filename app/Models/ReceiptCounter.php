<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptCounter extends Model
{
    protected $fillable = ['last_number'];

    public static function nextNumber(): int
    {
        $counter = self::lockForUpdate()->first();
        $counter->last_number += 1;
        $counter->save();
        return $counter->last_number;
    }
}