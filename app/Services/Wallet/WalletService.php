<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLedgerEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Get or create wallet for user
     */
    public function getWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
    }

    /**
     * Credit wallet (add money)
     */
    public function credit(
        Wallet $wallet,
        float $amount,
        ?Model $source = null,
        ?string $description = null
    ): WalletLedgerEntry {
        return DB::transaction(function () use ($wallet, $amount, $source, $description) {
            // Lock for update
            $wallet = Wallet::lockForUpdate()->find($wallet->id);

            // Idempotency check
            if ($source) {
                $existing = WalletLedgerEntry::where('source_type', get_class($source))
                    ->where('source_id', $source->id)
                    ->first();
                
                if ($existing) {
                    return $existing;
                }
            }

            $newBalance = (float) $wallet->balance + $amount;
            $wallet->update(['balance' => $newBalance]);

            return WalletLedgerEntry::create([
                'wallet_id' => $wallet->id,
                'type' => 'CREDIT',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'source_type' => $source ? get_class($source) : null,
                'source_id' => $source?->id,
                'description' => $description,
            ]);
        });
    }

    /**
     * Debit wallet (use money)
     */
    public function debit(
        Wallet $wallet,
        float $amount,
        ?Model $source = null,
        ?string $description = null
    ): WalletLedgerEntry {
        return DB::transaction(function () use ($wallet, $amount, $source, $description) {
            // Lock for update
            $wallet = Wallet::lockForUpdate()->find($wallet->id);

            // Insufficient balance check
            if ((float) $wallet->balance < $amount) {
                throw new \InvalidArgumentException('Saldo tidak mencukupi');
            }

            // Idempotency check
            if ($source) {
                $existing = WalletLedgerEntry::where('source_type', get_class($source))
                    ->where('source_id', $source->id)
                    ->first();
                
                if ($existing) {
                    return $existing;
                }
            }

            $newBalance = (float) $wallet->balance - $amount;
            $wallet->update(['balance' => $newBalance]);

            return WalletLedgerEntry::create([
                'wallet_id' => $wallet->id,
                'type' => 'DEBIT',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'source_type' => $source ? get_class($source) : null,
                'source_id' => $source?->id,
                'description' => $description,
            ]);
        });
    }

    /**
     * Get total system liability (sum of all wallet balances)
     */
    public function getTotalLiability(): float
    {
        return (float) Wallet::sum('balance');
    }

    /**
     * Get users with highest wallet balances
     */
    public function getTopUsersByBalance(int $limit = 10)
    {
        return Wallet::with('user')
            ->orderByDesc('balance')
            ->limit($limit)
            ->get();
    }
}
