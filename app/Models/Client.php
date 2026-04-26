<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'code',
        'full_name',
        'full_name_ar',
        'phone',
        'email',
        'password',
        'address',
        'national_id',
        'branch_id',
        'status',
        'login_enabled',
        'created_by',
    ];

    protected $hidden = [
        'password',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function accounts()
    {
        return $this->hasMany(ClientAccount::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAccountByCurrency($currencyId)
    {
        return $this->accounts()->where('currency_id', $currencyId)->first();
    }
    public function bailmentAccounts()
{
    return $this->hasMany(BailmentAccount::class);
}

public function bailmentTransactions()
{
    return $this->hasMany(BailmentTransaction::class);
}
}