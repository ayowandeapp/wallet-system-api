<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    use ApiResponses;

    /**
     * Display a paginated list of wallets.
     *
     * Retrieves wallets along with associated customer and merchant data and paginates the results
     * based on the specified length or defaults to 10.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $length = $request->length ?? 10;
        $wallets = Wallet::with(['customer', 'merchant'])->paginate($length);
        return $this->ok($wallets);
    }

    /**
     * Store a newly created wallet in storage.
     *
     * Validates the request to ensure that either a customer ID or merchant ID is provided, but not both.
     * Checks if a wallet already exists for the given customer or merchant and creates a new wallet if one does not exist.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'balance' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id|required_without:merchant_id',
            'merchant_id' => 'nullable|exists:merchants,id|required_without:customer_id'
        ]);


        if ($validator->fails()) {
            return $this->error($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        // If both customer_id and merchant_id are filled, return an error response
        if ($request->filled('customer_id') && $request->filled('merchant_id')) {
            return $this->error('Only one of customer_id or merchant_id must be provided, not both.', Response::HTTP_BAD_REQUEST);
        }

        // Check if a wallet already exists for the given customer or merchant
        $existingWallet = Wallet::where(function ($query) use ($request) {
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }
            if ($request->filled('merchant_id')) {
                $query->where('merchant_id', $request->merchant_id);
            }
        })->first();

        // If a wallet exists, return an error response
        if ($existingWallet) {
            return $this->error('A wallet already exists for this customer or merchant.', Response::HTTP_CONFLICT);
        }

        $wallet = Wallet::create($request->only(['customer_id', 'merchant_id']));
        return $this->success($wallet->refresh(), Response::HTTP_CREATED);
    }

    /**
     * Display the specified wallet.
     *
     * Retrieves and returns the wallet with the given ID along with its associated customer, merchant, and transaction data.
     * If the wallet is not found, a 404 error response is returned.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $wallet = Wallet::with(['customer', 'merchant', 'transactions'])->findOrFail((int) $id);
            return $this->ok($wallet);
        } catch (ModelNotFoundException $e) {
            return $this->error('Wallet not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified wallet from storage.
     *
     * This function is not implemented and will return a 405 Method Not Allowed response.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->error('Not implemented', Response::HTTP_METHOD_NOT_ALLOWED);
        /*

        try {
            $wallet = Wallet::findOrFail((int) $id);
            $wallet->delete();
            return $this->success(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return $this->error('Wallet not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Failed to delete the wallet', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        */
    }

    public function update(Request $request, $id)
    {
        return $this->error('Not implemented', Response::HTTP_METHOD_NOT_ALLOWED);
        /*
        try {
            $wallet = Wallet::findOrFail((int) $id);
            $wallet->update($request->all());
            return $this->ok($wallet);
        } catch (ModelNotFoundException $e) {
            return $this->error('Wallet not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Wallet update failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    */
    }
}
