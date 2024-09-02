<?php

namespace App\Actions;

use App\Models\Customer;

class CreateCustomerAction
{
    public function execute(array $request)
    {
        $customer = Customer::create($request);
        return $customer;
    }
}
