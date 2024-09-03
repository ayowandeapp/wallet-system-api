<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email'
    ];

    // public function Wallet(): HasOne
    // {
    //     return $this->hasOne(Wallet::class, 'customer_id', 'id');
    // }

    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'walletable');
    }
}
