<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{

    use ApiResponses;

    public function index(Request $request)
    {
        $length = $request->length ?? 10;
        $transactions = Transaction::with('wallet')->paginate($length);
        return $this->ok($transactions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:debit,credit'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $wallet = Wallet::findOrFail($request->wallet_id);
        if ($request->type == 'debit' && $wallet->balance < $request->amount) {
            return $this->error('Insufficient balance', Response::HTTP_BAD_REQUEST);
        }
        $transaction = Transaction::create($request->all());

        if ($request->type == 'credit') {
            $wallet->balance += $request->amount;
        } else {
            $wallet->balance -= $request->amount;
        }
        $wallet->save();
        return $this->success($transaction, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::with('wallet')->findOrFail((int) $id);
            return $this->ok($transaction);
        } catch (ModelNotFoundException $e) {
            return $this->error('Transaction not found', Response::HTTP_NOT_FOUND);
        }
    }

    public function walletTransactions(Request $request, $wallet)
    {

        $length = $request->length ?? 10;

        try {
            $wallet = Wallet::findOrFail($wallet);
            $transactions = $wallet->transactions()->paginate($length);
            return $this->ok($transactions);
        } catch (ModelNotFoundException $e) {
            return $this->error('Wallet not found', Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        return $this->error('Not implemented', Response::HTTP_METHOD_NOT_ALLOWED);
    }


    // public function destroy($id)
    // {
    //     $transaction = Transaction::findOrFail($id);
    //     $wallet = $transaction->wallet;

    //     // Revert wallet balance based on the transaction type
    //     if ($transaction->type == 'credit') {
    //         $wallet->balance -= $transaction->amount;
    //     } else {
    //         $wallet->balance += $transaction->amount;
    //     }
    //     $wallet->save();

    //     $transaction->delete();
    // }
}
