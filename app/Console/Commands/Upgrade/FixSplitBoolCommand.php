<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Donation;
use Illuminate\Console\Command;

class FixSplitBoolCommand extends Command
{
    protected $signature = 'upgrade:fix-split-bool';

    protected $description = 'Command description';

    public function handle(): void
    {
        $donations = Donation::with('donationSplits')->where('is_donation_splits_full', false)->get();


        foreach ($donations as $donation) {
            if ($donation->donationSplits->sum('amount') == $donation->amount) {
                $donation->update([
                    'is_donation_splits_full' => true
                ]);
            }
        }
    }
}
