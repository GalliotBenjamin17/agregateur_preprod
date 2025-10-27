<?php

namespace App\Console\Commands\Payzen;

use App\Models\Organization;
use App\Services\Models\TransactionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateTransactionCommand extends Command
{
    protected $signature = 'payzen:generate-payment-link';

    protected $description = 'Command description';

    public function handle()
    {
        $organization = Organization::first();

        if (is_null($organization)) {
            $this->error("Pas d'organisation");

            return Command::INVALID;
        }

        $transactionService = new TransactionService($organization->tenant);
        $transaction = $transactionService->createTransaction(related: $organization, amount: 1000);
    }

    public function manual()
    {
        /*
         *  user : 42452630
         *  pass test : testpassword_NXWrGxS3mklqUNVrTVJs7SGlLtrFCHrEPgXwCxl565gs3
         *  pass prod : prodpassword_rUYyOJEn2GPPoXpYwPg6I67XBi8Qs2zJnXoDWSb3Iwy7U
         *  serveur : https://api.payzen.eu
         *  clé publique de test : 42452630:testpublickey_lnAqgRGTEvKWju9HPIvZZznX7YDLu0N8upXzTGTdQKFM6
         *  Clé HMAC-SHA-256 de test (pour les verifs hash) : 51nkjd5Zw4JQNlugGPh152Lsytf49RBXQjzEQBLHrIRlb
         *  Playground : https://payzen.io/fr-FR/rest/V4.0/api/playground/Order/Get
         *
         */
        $orderId = \Str::orderedUuid();
        $this->info('Order ID : '.$orderId);

        $request = Http::withBasicAuth('42452630', 'testpassword_NXWrGxS3mklqUNVrTVJs7SGlLtrFCHrEPgXwCxl565gs3')
            ->post('https://api.payzen.eu/api-payment/V4/Charge/CreatePaymentOrder', [
                'amount' => 1000,
                'currency' => 'EUR',
                'orderId' => $orderId,
                'channelType' => 'URL',
                'channelOptions' => [
                    'mailOptions' => [
                        'recipient' => 'eliott.baylot@gmail.com',
                    ],
                ],
                'paymentReceiptEmail' => 'eliott.baylot@gmail.com',
                'returnMode' => 'GET',
                'successUrl' => 'https://native-media.fr/',
                'redirectSuccessTimeout' => 0,
                'formAction' => 'PAYMENT',
                'locale' => 'fr_FR',
                'merchantComment' => 'Coop Carbone',
                'customer' => [
                    'reference' => 'user_id_1',
                    'email' => 'eliott.baylot@gmail.com',

                    'shoppingCart' => [
                        'cartItemInfo' => [
                            [
                                'productLabel' => 'Crédits carbone',
                                'productAmount' => 1700,
                                'productRef' => 'project_id',
                                'productQty' => 8,
                            ],
                        ],
                    ],
                ],

                'expirationDate' => now()->addWeeks(4)->format('Y-m-d').'T00:00:00+00:00',
                'category' => 'COMPANY',
                'country' => 'FR',
            ]);

        dd($request->json());

        \Log::channel('daily')->info($request->body());
    }
}
