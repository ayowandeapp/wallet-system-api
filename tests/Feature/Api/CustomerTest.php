<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        $customers = Customer::factory(10)->create();
        $customersId = $customers->map(fn($cus) => $cus->id);

        $response = $this->get("api/customers");

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json('data');
        collect($data)->each(fn($cus) => $this->assertTrue(collect($customersId)->contains($cus['id'])));
    }


    public function test_store()
    {
        $dummy = Customer::factory()->make();

        $response = $this->post("/api/customers", $dummy->toArray());

        $response->assertStatus(Response::HTTP_CREATED);

        $data = $response->json('data');
        foreach ($dummy->toArray() as $key => $value) {
            $this->assertSame($data[$key], $value,  'Fillable is not the same');
        }
    }

    public function test_show(): void
    {
        $dummy = Customer::factory()->create();

        $response = $this->get("api/customers/{$dummy->id}");

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($dummy->id, $response->json('data.id'),  'Merchant ID not the same');
    }

    public function test_update()
    {
        $dummy = Customer::factory()->create();
        $customer = Customer::factory()->make();

        $fillables = (new Customer())->getFillable();

        $payload = [];
        foreach ($fillables as $key => $value) {
            $payload = array_merge($payload, [$value => $customer[$value]]);
        }
        $response = $this->patch("/api/customers/{$dummy->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json('data');
        // dd($data, $customer['name']);

        foreach ($fillables as $key => $value) {
            $this->assertSame($data[$value], $customer[$value],  'Failed to update ');
        }
    }

    public function test_delete()
    {
        $dummy = Customer::factory()->create();

        $response = $this->delete("/api/customers/{$dummy->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->expectException(ModelNotFoundException::class);
        Customer::findOrFail($dummy->id);
    }
}
