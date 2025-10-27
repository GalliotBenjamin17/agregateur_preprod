<?php

namespace App\Console\Commands\Payzen;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportAllTransactionsCommand extends Command
{
    protected $signature = 'payzen:import-all-transactions';

    protected $description = 'Command description';

    public function handle(): void
    {
        /*
         *  user : 42452630
         *  pass test : testpassword_NXWrGxS3mklqUNVrTVJs7SGlLtrFCHrEPgXwCxl565gs3
         *  serveur : https://api.payzen.eu
         *  clé publique de test : 42452630:testpublickey_lnAqgRGTEvKWju9HPIvZZznX7YDLu0N8upXzTGTdQKFM6
         *  Clé HMAC-SHA-256 de test (pour les verifs hash) : 51nkjd5Zw4JQNlugGPh152Lsytf49RBXQjzEQBLHrIRlb
         *  Playground : https://payzen.io/fr-FR/rest/V4.0/api/playground/Order/Get
         *
         */
        $request = Http::withBasicAuth('42452630', 'testpassword_NXWrGxS3mklqUNVrTVJs7SGlLtrFCHrEPgXwCxl565gs3')
            ->post('https://api.payzen.eu/api-payment/V4/Charge/PaymentOrder/Get', [
                'paymentOrderId' => 'b72a64307ad544b19923cc8356933d6d',
            ]);

        dd($request->json());

        \Log::channel('daily')->info($request->body());
    }
}
