<?php

namespace App\Traits\Emails;

use App\Models\Tenant;

trait WithSender
{
    /**
     * Return the right address.
     */
    public function getSenderAddress(?Tenant $tenant = null, bool $noply = false): string
    {
        if ($tenant) {
            if ($noply) {
                return $tenant->noreply_sender_email ?? config('mail.from.address');
            }

            return $tenant->sender_email ?? config('mail.from.address');
        }

        return config('mail.from.address');
    }

    public function getSenderName(?Tenant $tenant = null): string
    {
        if ($tenant) {
            return $tenant->name ?? config('mail.from.name');
        }

        return config('mail.from.name');
    }
}
