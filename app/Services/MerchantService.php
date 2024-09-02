<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Collection;

class MerchantService
{


    public function getMerchant(): Collection
    {
        return Merchant::all();
    }

    public function updateMerchant(int $id, array $request): Merchant
    {
        $Merchant = $this->findMerchant($id);
        $Merchant->update($request);
        return $Merchant;
    }

    public function findMerchant(int $id): Merchant
    {
        return Merchant::findOrFail((int) $id);
    }

    public function createMerchant(array $request): Merchant
    {
        return Merchant::create($request);
    }

    public function delete(int $id): void
    {
        $Merchant = $this->findMerchant($id);
        $Merchant->delete();
    }
}
