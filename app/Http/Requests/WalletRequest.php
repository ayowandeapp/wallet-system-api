<?php

namespace App\Http\Requests;

use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class WalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'walletable_id' => 'required|integer',
            'walletable_type' => 'required|string'
        ];
    }

    public function after()
    {
        return [
            function (Validator $validator) {
                // Check if a wallet already exist
                $existingWallet = Wallet::where([
                    'walletable_id' => $this->walletable_id,
                    'walletable_type' => $this->walletable_type
                ])->first();

                // If a wallet exists, return an error response
                if ($existingWallet) {
                    $validator->errors()->add('walletable_id', 'A wallet already exists.');
                }
            }
        ];
    }
}
