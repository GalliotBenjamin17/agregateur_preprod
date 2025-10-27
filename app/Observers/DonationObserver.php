<?php

namespace App\Observers;

use App\Helpers\DonationHelper;
use App\Models\Donation;

class DonationObserver
{
    public function created(Donation $donation): void
    {
        DonationHelper::generateCertificate(donation: $donation);
    }

    public function updated(Donation $donation): void
    {
    }

    public function deleted(Donation $donation): void
    {
    }

    public function restored(Donation $donation): void
    {
    }

    public function forceDeleted(Donation $donation): void
    {
    }
}
