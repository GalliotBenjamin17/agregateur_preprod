<?php

namespace App\Observers\Models;

use App\Models\ProjectHolderPayment;

class ProjectHolderPaymentObserver
{
    public function created(ProjectHolderPayment $projectHolderPayment): void
    {
        $projectHolderPayment->project->calculateHolderPaymentsSum();
    }

    public function updated(ProjectHolderPayment $projectHolderPayment): void
    {
        $projectHolderPayment->project->calculateHolderPaymentsSum();
    }

    public function deleted(ProjectHolderPayment $projectHolderPayment): void
    {
        $projectHolderPayment->project->calculateHolderPaymentsSum();
    }
}
