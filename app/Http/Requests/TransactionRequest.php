<?php

namespace App\Http\Requests;

use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TransactionRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:debit,credit'
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $wallet = Wallet::findOrFail($this->wallet_id);
                if ($this->type == 'debit' && $wallet->balance < $this->amount) {
                    $validator->errors()->add(
                        'amount',
                        'Insufficient balance!'
                    );
                }
            }
        ];
    }
}
