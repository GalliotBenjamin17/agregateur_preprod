<?php

namespace App\Observers;

use App\Models\Partner;

class PartnerObserver
{
    public function creating(Partner $partner): void
    {
        $partner->created_by = $partner->created_by ?: request()->user()?->id;
    }

    public function created(Partner $partner): void
    {

    }

    public function updated(Partner $partner): void
    {
    }

    public function deleted(Partner $partner): void
    {
    }

    public function restored(Partner $partner): void
    {
    }

    public function forceDeleted(Partner $partner): void
    {
    }
}
