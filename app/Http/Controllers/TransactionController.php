<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{

    use ApiResponses;

    /**
     * Display a paginated list of transactions.
     *
     * Retrieves transactions with associated wallet data and paginates the results
     * based on the specified length or defaults to 10.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $length = $request->length ?? 10;
        $transactions = Transaction::with('wallet')->paginate($length);
        return $this->ok($transactions);
    }

    /**
     * Store a newly created transaction in storage.
     *
     * Validates the request, checks wallet balance for debit transactions, creates
     * a transaction record, updates the wallet balance, and handles errors with transactions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
        try {
            DB::beginTransaction();

            $transaction = Transaction::create($request->all());

            if ($request->type == 'credit') {
                $wallet->balance += $request->amount;
            } else {
                $wallet->balance -= $request->amount;
            }
            $wallet->save();

            DB::commit();
            return $this->success($transaction, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->error('Process Unsuccessful!', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified transaction.
     *
     * Retrieves and returns the transaction with the given ID along with its associated wallet data.
     * If the transaction is not found, a 404 error response is returned.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::with('wallet')->findOrFail((int) $id);
            return $this->ok($transaction);
        } catch (ModelNotFoundException $e) {
            return $this->error('Transaction not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display a paginated list of transactions for a specific wallet.
     *
     * Retrieves transactions for the specified wallet and paginates the results
     * based on the specified length or defaults to 10. If the wallet is not found,
     * a 404 error response is returned.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $wallet
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Update the specified transaction in storage.
     *
     * This function is not implemented and will return a 405 Method Not Allowed response.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
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
