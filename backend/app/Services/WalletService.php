<?php

namespace App\Services;

use App\Models\BusRoute;
use App\Models\Device;
use App\Models\FareTransaction;
use App\Models\NfcCard;
use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function __construct(
        protected AuditService $auditService,
        protected NfcResolverService $nfcResolver,
    ) {}

    public function fund(Wallet $wallet, float $amount, User $performer, ?string $description = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Amount must be greater than zero.']);
        }

        return DB::transaction(function () use ($wallet, $amount, $performer, $description) {
            $locked = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
            $newBalance = (float) $locked->balance + $amount;
            $locked->update(['balance' => $newBalance]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $locked->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'WF-'.Str::upper(Str::random(10)),
                'description' => $description ?? 'Manual wallet funding',
                'performed_by' => $performer->id,
            ]);

            $this->auditService->log('wallet.funded', Wallet::class, $locked->id, [
                'amount' => $amount,
                'reference' => $transaction->reference,
            ], $performer->id);

            return $transaction;
        });
    }

    public function deductBusFare(
        Student $student,
        NfcCard $card,
        BusRoute $route,
        User $user,
        Device $device,
    ): array {
        return DB::transaction(function () use ($student, $card, $route, $user, $device) {
            $wallet = Wallet::where('student_id', $student->id)->lockForUpdate()->first();

            if (! $wallet || $wallet->status !== 'active') {
                throw ValidationException::withMessages(['wallet' => 'Student wallet is not available.']);
            }

            $fare = (float) $route->fare_amount;

            if ((float) $wallet->balance < $fare) {
                $this->nfcResolver->recordTap($card->uid, $card, $student, $user, $device, 'bus_fare', 'insufficient_balance');

                throw ValidationException::withMessages(['wallet' => 'Insufficient wallet balance.']);
            }

            $newBalance = (float) $wallet->balance - $fare;
            $wallet->update(['balance' => $newBalance]);

            $walletTxn = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $fare,
                'balance_after' => $newBalance,
                'reference' => 'BF-'.Str::upper(Str::random(10)),
                'description' => "Bus fare: {$route->name}",
                'performed_by' => $user->id,
                'device_id' => $device->id,
                'metadata' => ['bus_route_id' => $route->id],
            ]);

            $fareTxn = FareTransaction::create([
                'wallet_id' => $wallet->id,
                'student_id' => $student->id,
                'bus_route_id' => $route->id,
                'nfc_card_id' => $card->id,
                'device_id' => $device->id,
                'processed_by' => $user->id,
                'wallet_transaction_id' => $walletTxn->id,
                'amount' => $fare,
                'status' => 'completed',
                'reference' => $walletTxn->reference,
            ]);

            $this->nfcResolver->recordTap($card->uid, $card, $student, $user, $device, 'bus_fare', 'success', [
                'fare_transaction_id' => $fareTxn->id,
            ]);

            $this->auditService->log('bus.fare_deducted', FareTransaction::class, $fareTxn->id, [
                'amount' => $fare,
                'route' => $route->name,
            ], $user->id, $device->id);

            return [
                'fare_transaction' => $fareTxn,
                'wallet_transaction' => $walletTxn,
                'balance' => $newBalance,
            ];
        });
    }
}
