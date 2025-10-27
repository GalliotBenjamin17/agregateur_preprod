<?php

namespace App\Services\Models;

use App\Helpers\TVAHelper;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\Transactions\RequestPaymentNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TransactionService
{
    const EXPIRATION_DAYS = 90;

    public function __construct(
        public Tenant $tenant
    ) {
    }

    public function createTransaction(Organization|User $related, float $amount, ?Project $project = null, ?string $failedUrl = null): Transaction
    {
        $auth = $this->getPayzenAuth();

        $orderId = Str::uuid();

        $request = Http::withBasicAuth($auth['user_id'], $auth['password'])
            ->post('https://api.payzen.eu/api-payment/V4/Charge/CreatePaymentOrder', [
                'amount' => round($amount * 100, 0),
                'currency' => 'EUR',
                'orderId' => $orderId,
                'channelType' => 'URL',
                'paymentReceiptEmail' => match (get_class($related)) {
                    Organization::class => $related->billing_email,
                    User::class => $related->email,
                },
                'returnMode' => 'GET',
                'cancelUrl' => $failedUrl ?? route('transactions.webhooks.failed'),
                'successUrl' => route('transactions.webhooks.success'),
                'redirectSuccessTimeout' => 0,
                'formAction' => 'PAYMENT',
                'paymentMethods' => match (get_class($related)) {
                    Organization::class => ['VISA', 'MASTERCARD', 'CB', 'SDD'],
                    User::class => ['VISA', 'MASTERCARD', 'CB'],
                },
                'locale' => 'fr_FR',
                'merchantComment' => $this->tenant->name,
                'customer' => [
                    'reference' => $related->id,
                    'email' => match (get_class($related)) {
                        Organization::class => $related->billing_email,
                        User::class => $related->email,
                    },
                    'billingDetails' => [
                        'category' => 'COMPANY',
                    ],
                ],
                'expirationDate' => $this->getExpirationDate(),
                'country' => 'FR',
            ]);

        $response = $request->json();

        return $this->storeTransaction($related, $response, $orderId, $project);
    }

    protected function storeTransaction(Organization|User $related, $response, string $orderId, ?Project $project = null): Transaction
    {
        return Transaction::create([
            'tenant_id' => $this->tenant->id,
            'related_id' => $related->id,
            'related_type' => get_class($related),
            'project_id' => $project?->id,
            'order_id' => $orderId,
            'payment_order_id' => \Arr::get($response, 'answer.paymentOrderId'),
            'payment_url' => \Arr::get($response, 'answer.paymentURL'),
            'status' => \Arr::get($response, 'answer.paymentOrderStatus'),
            'amount' => \Arr::get($response, 'answer.amount'),
            'tax_amount' => \Arr::get($response, 'answer.taxAmount') ?? \Arr::get($response, 'answer.amount') * TVAHelper::TVA_PERCENTAGE,
            'expiration_at' => $this->getExpirationDate(),
            'category' => 'COMPANY',
            'merchant_comment' => $this->tenant->name,
            'customer_reference' => $related->id,
            'customer_email' => match (get_class($related)) {
                Organization::class => $related->billing_email,
                User::class => $related->email,
            },
            'created_by' => request()->user()?->id,
        ]);
    }

    protected function getExpirationDate(): string
    {
        return now()->addDays(self::EXPIRATION_DAYS)->format('Y-m-d').'T00:00:00+00:00';
    }

    public function getPayzenAuth(): array
    {
        return [
            'user_id' => $this->tenant->payzen_user_id,
            'password' => match ($this->tenant->payments_mode_test) {
                true => $this->tenant->payzen_password_test,
                false => $this->tenant->payzen_password_prod,
                default => $this->tenant->payzen_password_test,
            },
        ];
    }

    public function sendEmail(Transaction $transaction, ?string $email = null, ?string $text = null): void
    {
        try {
            Notification::route('mail', $email ?? $transaction->related->billing_email)
                ->notify(new RequestPaymentNotification(transaction: $transaction, text: $text));

            \Filament\Notifications\Notification::make()
                ->title('Email envoyé')
                ->body("L'email a été transmis sur l'email renseigné.")
                ->success()
                ->send();
        } catch (\Exception $e) {
            dd($e);
            \Filament\Notifications\Notification::make()
                ->title('Email non envoyé')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
