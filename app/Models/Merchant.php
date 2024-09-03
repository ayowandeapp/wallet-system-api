<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email'
    ];


    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'walletable');
    }
}
