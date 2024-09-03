<?php

namespace Tests\Feature\Api;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_wallet_transaction_history_in_paginated_format()
    {
        $wallet = Wallet::factory()->create();
        $wallet->transactions()->createMany(Transaction::factory()->count(20)->make()->toArray());

        $response = $this->get("/api/wallets/{$wallet->id}/transactions?page=1&per_page=10");

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                '*' => [],
                'data' => [
                    '*' => ['id', 'wallet_id', 'type', 'amount']
                ],
                'meta',
            ]
        ]);
    }
}
