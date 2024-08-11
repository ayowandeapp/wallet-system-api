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

    public function index(Request $request)
    {
        $merchants = Merchant::get();
        return $this->ok($merchants);
    }

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

    public function show($id)
    {
        try {
            $merchant = Merchant::findOrFail((int) $id);
            return $this->ok($merchant);
        } catch (ModelNotFoundException $e) {
            return $this->error('Merchant not found', Response::HTTP_NOT_FOUND); // Return a 404 response with an error message
        }
    }

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
