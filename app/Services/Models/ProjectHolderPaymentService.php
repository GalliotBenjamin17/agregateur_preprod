<?php

namespace App\Services\Models;

use App\Models\ProjectHolderPayment;

class ProjectHolderPaymentService
{
    public function __construct(
        public ?ProjectHolderPayment $projectHolderPayment = null
    ) {
    }

    public function store(array $data): ProjectHolderPayment
    {
        $this->projectHolderPayment = ProjectHolderPayment::create($data);

        return $this->projectHolderPayment;
    }

    public function update(array $data): ProjectHolderPayment
    {
        $this->projectHolderPayment->update($data);

        return $this->projectHolderPayment->refresh();
    }
}
