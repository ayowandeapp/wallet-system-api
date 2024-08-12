<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MerchantController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of all merchants.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $merchants = Merchant::get();
        return $this->ok($merchants);
    }

    /**
     * Store a newly created merchant in storage.
     *
     * Validates the incoming request and creates a new merchant record.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchants'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        $merchant = Merchant::create($request->all());
        return $this->success($merchant, Response::HTTP_CREATED);
    }

    /**
     * Display the specified merchant.
     *
     * Retrieves and returns the merchant with the given ID. If the merchant is not found,
     * a 404 error response is returned.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $merchant = Merchant::findOrFail((int) $id);
            return $this->ok($merchant);
        } catch (ModelNotFoundException $e) {
            return $this->error('Merchant not found', Response::HTTP_NOT_FOUND); // Return a 404 response with an error message
        }
    }

    /**
     * Update the specified merchant in storage.
     *
     * Validates the incoming request and updates the merchant record with the given ID.
     * If the merchant is not found, a 404 error response is returned. If the update fails,
     * a 500 error response is returned.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        try {
            $merchant = Merchant::findOrFail((int) $id);
            $merchant->update($request->all());
            return $this->ok($merchant);
        } catch (ModelNotFoundException $e) {
            return $this->error('Merchant not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Merchant update failed', Response::HTTP_INTERNAL_SERVER_ERROR); // Return a 404 response with an error message
        }
    }

    /**
     * Remove the specified merchant from storage.
     *
     * Deletes the merchant with the given ID. If the merchant is not found,
     * a 404 error response is returned. If the deletion fails, a 500 error response is returned.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $merchant = Merchant::findOrFail((int) $id);
            $merchant->delete();
            return $this->success(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return $this->error('Merchant not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Failed to delete the merchant', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
