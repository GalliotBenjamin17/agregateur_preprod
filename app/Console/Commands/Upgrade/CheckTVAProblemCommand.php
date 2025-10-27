<?php

namespace App\Console\Commands\Upgrade;

use App\Helpers\TVAHelper;
use App\Models\DonationSplit;
use Illuminate\Console\Command;

class CheckTVAProblemCommand extends Command
{
    protected $signature = 'upgrade:check-tva-problem';

    protected $description = 'Command description';

    public function handle(): void
    {
        $this->checkErrors(makeUpdate: false);
        $this->checkErrors(makeUpdate: true);
        $this->checkErrors(makeUpdate: false);
    }

    public function checkErrors(bool $makeUpdate = false): void
    {

        $donationsSplits = DonationSplit::with([
            'projectCarbonPrice',
        ])->get();

        $counter = 0;

        foreach ($donationsSplits as $donationsSplit) {

            $tonnesCo2Need = round($donationsSplit->amount / TVAHelper::getTTC($donationsSplit->projectCarbonPrice->price), 2);

            if ($tonnesCo2Need != round($donationsSplit->tonne_co2, 2)) {

                if ($makeUpdate) {
                    $donationsSplit->update([
                        'tonne_co2' => $tonnesCo2Need,
                    ]);
                }

                $counter += 1;
            }
        }

        $this->comment("Problems : $counter vs.  ".DonationSplit::count());
    }
}
