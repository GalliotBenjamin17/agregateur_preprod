<?php

namespace App\Observers;

use App\Models\PartnerProjectPayment;

class PartnerProjectPaymentObserver
{
    public function creating(PartnerProjectPayment $partnerProjectPayment): void
    {
        $partnerProjectPayment->created_by = $partnerProjectPayment->created_by ?: request()->user()?->id;
    }

    public function created(PartnerProjectPayment $partnerProjectPayment): void
    {
    }

    public function updated(PartnerProjectPayment $partnerProjectPayment): void
    {
    }

    public function deleted(PartnerProjectPayment $partnerProjectPayment): void
    {
    }

    public function restored(PartnerProjectPayment $partnerProjectPayment): void
    {
    }

    public function forceDeleted(PartnerProjectPayment $partnerProjectPayment): void
    {
    }
}
