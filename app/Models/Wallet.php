<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'walletable_id',
        'walletable_type'
    ];

    // public function customer(): BelongsTo
    // {
    //     return $this->belongsTo(Customer::class, 'customer_id', 'id');
    // }

    // public function merchant(): BelongsTo
    // {
    //     return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    // }
    public function walletable(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'wallet_id', 'id');
    }
}
