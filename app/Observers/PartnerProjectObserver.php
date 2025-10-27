<?php

namespace App\Observers;

use App\Models\PartnerProject;

class PartnerProjectObserver
{
    public function creating(PartnerProject $partnerProject): void
    {
        $partnerProject->created_by = $partnerProject->created_by ?: request()->user()?->id;
    }

    public function created(PartnerProject $partnerProject): void
    {
    }

    public function updated(PartnerProject $partnerProject): void
    {
    }

    public function deleted(PartnerProject $partnerProject): void
    {
    }

    public function restored(PartnerProject $partnerProject): void
    {
    }

    public function forceDeleted(PartnerProject $partnerProject): void
    {
    }
}
