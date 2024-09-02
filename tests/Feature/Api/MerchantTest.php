<?php

namespace Tests\Feature\Api;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;

class MerchantTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        //load dummy data
        $merchants = Merchant::factory(10)->create();
        $merchantsId = $merchants->map(fn($merchant) => $merchant->id);

        //call endpoint
        $response = $this->get("/api/merchants");

        //assert status
        $response->assertStatus(Response::HTTP_OK);

        //verify records
        $data = $response->json('data');
        collect($data)->each(fn($merchant) => $this->assertTrue(collect($merchantsId)->contains($merchant['id'])));
    }

    public function test_store()
    {
        $dummy = Merchant::factory()->make();

        $response = $this->post("/api/merchants", $dummy->toArray());

        $response->assertStatus(Response::HTTP_CREATED);

        $data = $response->json('data');
        foreach ($dummy->toArray() as $key => $value) {
            $this->assertSame($data[$key], $value,  'Fillable is not the same');
        }
    }

    public function test_show(): void
    {
        $dummy = Merchant::factory()->create();

        $response = $this->get("api/merchants/{$dummy->id}");

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($dummy->id, $response->json('data.id'),  'Merchant ID not the same');
    }

    public function test_update()
    {
        $dummy = Merchant::factory()->create();
        $merchant = Merchant::factory()->make();

        $fillables = (new Merchant())->getFillable();

        $payload = [];
        foreach ($fillables as $key => $value) {
            $payload = array_merge($payload, [$value => $merchant[$value]]);
        }
        $response = $this->patch("/api/merchants/{$dummy->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json('data');

        foreach ($fillables as $key => $value) {

            $this->assertSame($data[$value], $merchant[$value],  'Failed to update ');
        }
    }

    public function test_delete()
    {
        $dummy = Merchant::factory()->create();

        $response = $this->delete("/api/merchants/{$dummy->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->expectException(ModelNotFoundException::class);
        Merchant::findOrFail($dummy->id);
    }
}
