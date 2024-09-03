<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;

class WalletService
{
    public function getWallet(int $length)
    {
        return Wallet::with(['walletable'])->paginate($length);
    }

    public function updateWallet(int $id, array $request): Wallet
    {
        $Wallet = $this->findWallet($id);
        $Wallet->update($request);
        return $Wallet;
    }

    public function findWallet(int $id): Wallet
    {
        return Wallet::with(['walletable'])->findOrFail($id);
    }

    public function createWallet(array $request): Wallet
    {
        return Wallet::create($request);
    }

    public function delete(int $id): void
    {
        $Wallet = $this->findWallet($id);
        $Wallet->delete();
    }
}
