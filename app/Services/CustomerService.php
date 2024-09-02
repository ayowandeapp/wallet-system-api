<?php

namespace App\Services;

use App\Actions\CreateCustomerAction;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerService
{

    public function getCustomer(): Collection
    {
        return Customer::all();
    }

    public function updateCustomer(int $id, array $request): Customer
    {
        $customer = $this->findCustomer($id);
        $customer->update($request);
        return $customer;
    }

    public function findCustomer(int $id): Customer
    {
        return Customer::findOrFail((int) $id);
    }

    public function createCustomer(array $request): Customer
    {
        return (new CreateCustomerAction)->execute($request);
    }

    public function delete(int $id): void
    {
        $customer = $this->findCustomer($id);
        $customer->delete();
    }
}
