<?php

namespace App\Console\Commands\Imports;

use App\Models\Donation;
use Illuminate\Console\Command;

class FlagDonationAsCompleteCommand extends Command
{
    protected $signature = 'imports:flag-donations-as-complete';

    protected $description = 'Command description';

    public function handle(): void
    {
        $donations = Donation::withSum('donationSplits', 'amount')->get();

        foreach ($donations as $donation) {
            if ($donation->amount == $donation->donation_splits_sum_amount) {
                $donation->update([
                    'is_donation_splits_full' => true,
                ]);
            }
        }

    }
}
