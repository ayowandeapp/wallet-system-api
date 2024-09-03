<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'walletable_id' => $this->walletable_id,
            'walletable_type' => $this->walletable_type,
            'balance' => $this->balance,
            'walletable' => $this->walletable,
        ];
    }
}
