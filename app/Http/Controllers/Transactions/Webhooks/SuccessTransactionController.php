<?php

namespace App\Http\Controllers\Transactions\Webhooks;

use App\Enums\Roles;
use App\Helpers\DonationHelper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\Transactions\ConfirmationPaymentNotification;
use App\Services\Models\DonationService;
use Illuminate\Http\Request;

class SuccessTransactionController extends Controller
{
    public function __invoke(Request $request)
    {
        $transaction = Transaction::where('order_id', $request->get('vads_order_id'))
            ->firstOrFail();

        $donationService = new DonationService();
        $donation = $donationService->storeFromTransaction(transaction: $transaction, externalId: $request->get('vads_trans_uuid'));

        if ($transaction->project_id) {
            $splits = [
                [
                    'type' => 'project',
                    'data' => [
                        'project_id' => $transaction->project_id,
                        'amount' => $transaction->amount / 100,
                    ],
                ],
            ];

            DonationHelper::buildSplit(donation: $donation, splits: $splits);
        }

        if ($transaction->related instanceof User) {
            $user = User::whereId($transaction->related_id)->first();

            if ($user->hasRole(Roles::Subscriber)) {
                $user->syncRoles(Roles::Contributor); // Automatic remove Subscriber
            }
        }

        $request->user()->notifyNow(new ConfirmationPaymentNotification(donation: $donation));

        if ($request->user()->hasRole(Roles::Admin)) {
            return to_route('donations.index');
        }

        return to_route('tenant.donations.confirmation', ['tenant' => $donation->tenant, 'donation' => $donation]);
    }
}
