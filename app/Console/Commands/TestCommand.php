<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestCommand extends Command
{
    protected $signature = 'test:command';

    protected $description = 'Command description';

    public function handle(): void
    {
        //0b833b1e-0096-44e4-a181-95d50edfc752
        $auth = $this->getPayzenAuth();

        $request = Http::withBasicAuth($auth['user_id'], $auth['password'])
            ->post('https://api.payzen.eu/api-payment/V4/Order/Get', [
                'orderId' => '0b833b1e-0096-44e4-a181-95d50edfc752',
            ]);

        $response = $request->json();
        dd($response);
    }

    public function getPayzenAuth(): array
    {
        $tenant = Tenant::first();

        return [
            'user_id' => $tenant->payzen_user_id,
            'password' => match ($tenant->payments_mode_test) {
                true => $tenant->payzen_password_test,
                false => $tenant->payzen_password_prod,
                default => $tenant->payzen_password_test,
            },
        ];
    }
}
