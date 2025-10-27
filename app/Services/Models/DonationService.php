<?php

namespace App\Services\Models;

use App\Models\Donation;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;

class DonationService
{
    public function storeFromTransaction(Transaction $transaction, string $externalId): Donation
    {
        $donation = Donation::create([
            'tenant_id' => $transaction->tenant_id,
            'related_type' => $transaction->related_type,
            'related_id' => $transaction->related_id,
            'source' => 'bank_account',
            'external_id' => $externalId,
            'amount' => $transaction->amount / 100,
            'created_by' => $transaction->created_by,
        ]);

        $transaction->update([
            'status' => 'PAID',
            'donation_id' => $donation->id,
        ]);

        return $donation;
    }

    public function storeFromTerminal(Tenant $tenant, float $amount, User $user, array $sourceInformations = []): Donation
    {
        return Donation::create([
            'tenant_id' => $tenant->id,
            'source' => 'terminal',
            'amount' => $amount,
            'related_type' => get_class($user),
            'related_id' => $user->id,
            'source_informations' => $sourceInformations,
        ]);
    }
}
