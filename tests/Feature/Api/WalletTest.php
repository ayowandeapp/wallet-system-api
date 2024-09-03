<?php

namespace Tests\Feature\Api;

use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class WalletTest extends TestCase
{

    use RefreshDatabase;

    public function test_store()
    {
        $dummy = Wallet::factory()->make();

        $response = $this->post('api/wallets', $dummy->toArray());

        $response->assertStatus(Response::HTTP_CREATED);
        $data = $response->json('data');

        foreach ($dummy->toArray() as $key => $value) {
            $this->assertSame($data[$key], $value, 'Error: not the same');
        }
    }

    public function test_show()
    {
        $dummy = Wallet::factory()->create();

        $response = $this->get("api/wallets/{$dummy->id}");

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($dummy->id, $response->json('data.id'),  'Wallet ID not the same');
    }

    public function test_index()
    {
        $wallets = Wallet::factory(10)->create();

        $response = $this->get("api/wallets?page=1&per_page=10");

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                '*' => [],
                'data' => [
                    '*' => ['id', 'walletable_id', 'walletable_type', 'balance']

                ],
                'meta',
            ]
        ]);
    }

    public function test_can_credit_and_debit_wallet()
    {
        //dummy wallet
        $dummy = Wallet::factory()->create();

        //credit the wallet with 5000
        $credit_response = $this->post('/api/transactions', [
            'amount' => 5000,
            'wallet_id' => $dummy['id'],
            'type' => 'credit'
        ]);
        $credit_response->assertStatus(Response::HTTP_CREATED);
        $credit_data = $credit_response->json('data');

        $this->assertEquals($credit_data['amount'], $dummy->refresh()['balance']);

        //debit the wallet with 4000
        $debit_response = $this->post('api/transactions', [
            'amount' => 4000,
            'wallet_id' => $dummy['id'],
            'type' => 'debit'
        ]);
        $debit_response->assertStatus(Response::HTTP_CREATED);
        $debit_data = $debit_response->json('data');

        $this->assertEquals($debit_data['amount'], 4000);

        $this->assertEquals(1000, $dummy->refresh()['balance']);
    }
}
