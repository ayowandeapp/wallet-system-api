<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionService
{
    public function getTransaction(int $length)
    {
        return Transaction::paginate($length);
    }

    public function updateTransaction(int $id, array $request): Transaction
    {
        $Transaction = $this->findTransaction($id);
        $Transaction->update($request);
        return $Transaction;
    }

    public function findTransaction(int $id): Transaction
    {
        return Transaction::findOrFail($id);
    }

    public function createTransaction(array $request): Transaction
    {
        $transaction = Transaction::create($request);

        $wallet = (new WalletService)->findWallet($request['wallet_id']);

        if ($request['type'] == 'credit') {
            $wallet->balance += $request['amount'];
        } else {
            $wallet->balance -= $request['amount'];
        }
        $wallet->save();

        return $transaction;
    }

    public function getWalletTransactions(int $id, int $length)
    {
        $wallet = (new WalletService)->findWallet($id);
        return $wallet->transactions()->paginate($length);
    }
}
