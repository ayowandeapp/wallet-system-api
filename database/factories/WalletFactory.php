<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $isMerchant = fake()->boolean();

        return [
            'customer_id' => !$isMerchant ? Customer::factory() : null,
            'merchant_id' => $isMerchant ? Merchant::factory() : null,
            'balance' => number_format(0, 2)
        ];
    }
}
