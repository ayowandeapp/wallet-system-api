<?php

namespace App\Http\Controllers;

use App\Actions\CreateCustomerAction;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    use ApiResponses;

    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of all customers.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $customers = $this->customerService->getCustomer();
        return $this->success(CustomerResource::collection($customers));
    }

    /**
     * Store a newly created customer in storage.
     *
     * Validates the incoming request and creates a new customer record.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustomerRequest $request)
    {
        try {
            $customer = $this->customerService->createCustomer($request->validated());
            return $this->success(new CustomerResource($customer), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified customer.
     *
     * Retrieves and returns the customer with the given ID. If the customer is not found,
     * a 404 error response is returned.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $customer = $this->customerService->findCustomer($id);
            return $this->success(new CustomerResource($customer), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified customer in storage.
     *
     * Validates the incoming request and updates the customer record with the given ID.
     * If the customer is not found, a 404 error response is returned. If the update fails,
     * a 500 error response is returned.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCustomerRequest $request, int $id)
    {
        try {
            $customer = $this->customerService->updateCustomer($id, $request->validated());
            return $this->success(new CustomerResource($customer), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Customer update failed', Response::HTTP_INTERNAL_SERVER_ERROR); // Return a 404 response with an error message
        }
    }

    /**
     * Remove the specified customer from storage.
     *
     * Deletes the customer with the given ID. If the customer is not found,
     * a 404 error response is returned. If the deletion fails, a 500 error response is returned.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->customerService->delete($id);
            return $this->success(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->error('Failed to delete the customer', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
