<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $customers = Customer::all();
        return $this->ok($customers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $customer = Customer::create($request->all());
        return $this->success($customer, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        try {
            $customer = Customer::findOrFail((int) $id);
            return $this->ok($customer);
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found', Response::HTTP_NOT_FOUND);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail((int) $id);
            $customer->update($request->all());
            return $this->ok($customer);
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Customer update failed', Response::HTTP_INTERNAL_SERVER_ERROR); // Return a 404 response with an error message
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail((int) $id);
            $customer->delete();
            return $this->success(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Failed to delete the customer', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
