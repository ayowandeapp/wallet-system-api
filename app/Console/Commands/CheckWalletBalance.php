<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckWalletBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallets:check-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Identify wallets with balances that do not match their transactions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Wallet balance check started.');

        Wallet::chunk(100, function ($wallets) {
            foreach ($wallets as $wallet) {
                $expectedBalance = Transaction::where('wallet_id', $wallet->id)
                    ->selectRaw('
                        SUM(
                            CASE
                            WHEN type = "debit" THEN amount *-1
                            ELSE amount
                            END
                        ) as balance')->value('balance');

                // Compare expected balance with the actual balance
                if ($wallet->balance != $expectedBalance) {
                    // Log the discrepancy
                    Log::warning("Wallet ID {$wallet->id} has a mismatched balance. Expected: {$expectedBalance}, Actual: {$wallet->balance}");
                }
            }
        });
        $this->info('Wallet balance check completed.');
    }
}
