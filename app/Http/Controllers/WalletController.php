<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletRequest;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletResourceCollection;
use App\Models\Wallet;
use App\Services\WalletService;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    use ApiResponses;

    public function __construct(private WalletService $walletService)
    {
        # code...
    }

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
        $wallets = $this->walletService->getWallet($length);
        return $this->success(new WalletResourceCollection($wallets));
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
    public function store(WalletRequest $request)
    {
        try {
            $wallet = $this->walletService->createWallet($request->only(['walletable_id', 'walletable_type']));
            return $this->success(new WalletResource($wallet->refresh()), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), Response::HTTP_CONFLICT);
        }
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
    public function show(int $id)
    {
        try {
            $wallet = $this->walletService->findWallet($id);
            return $this->success(new WalletResource($wallet), Response::HTTP_OK);
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
