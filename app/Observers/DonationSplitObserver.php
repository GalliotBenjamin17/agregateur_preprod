<?php

namespace App\Observers;

use App\Helpers\DonationHelper;
use App\Models\DonationSplit;
use App\Notifications\Projects\FullyFinancedNotification;
use App\Notifications\Projects\SemiFinancedNotification;
use Illuminate\Support\Facades\Notification;

class DonationSplitObserver
{
    public function created(DonationSplit $donationSplit): void
    {
        DonationHelper::generateCertificate(donation: $donationSplit->donation);

        if ($donationSplit->project and ! is_null($donationSplit->project->cost_global_ttc) and $donationSplit->project->cost_global_ttc != 0) {

            $project = $donationSplit->project;

            $donationsAffiliated = $project->donationSplits->sum('amount');
            $percentage = round(($donationsAffiliated / $project->cost_global_ttc) * 100);

            // if ($percentage >= 50 and ! $project->is_semi_financed_notification_sent) {
            //     $emails = $project->getOrganizationsAndUsersEmails();
            //     foreach ($emails as $email) {
            //         Notification::route('mail', $email)
            //             ->notify(new SemiFinancedNotification(project: $project));
            //     }
            //     $project->update([
            //         'is_semi_financed_notification_sent' => true,
            //     ]);
            // }

            // if ($percentage >= 100 and ! $project->is_fully_financed_notification_sent) {
            //     $emails = $project->getOrganizationsAndUsersEmails();
            //     foreach ($emails as $email) {
            //         if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //             continue;
            //         }
            //         Notification::route('mail', $email)
            //             ->notify(new FullyFinancedNotification(project: $project));
            //     }
            //     $project->update([
            //         'is_fully_financed_notification_sent' => true,
            //     ]);
            // }
        }
    }

    public function updated(DonationSplit $donationSplit): void
    {
    }

    public function deleted(DonationSplit $donationSplit): void
    {
    }

    public function restored(DonationSplit $donationSplit): void
    {
    }

    public function forceDeleted(DonationSplit $donationSplit): void
    {
    }
}
