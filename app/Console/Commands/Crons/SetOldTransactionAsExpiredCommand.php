<?php

namespace App\Console\Commands\Crons;

use App\Models\Transaction;
use Illuminate\Console\Command;

class SetOldTransactionAsExpiredCommand extends Command
{
    protected $signature = 'crons:set-old-transaction-as-expired';

    protected $description = 'Command description';

    public function handle(): void
    {
        Transaction::where('expiration_at', '>', now())->update([
            'status' => 'ABANDONED',
        ]);
    }
}
