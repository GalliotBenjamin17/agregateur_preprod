<?php

namespace App\Services\Models;

use App\Models\PartnerProjectPayment;

class PartnerProjectPaymentService
{
    public function __construct(
        public ?PartnerProjectPayment $partnerProjectPayment = null
    ) {
    }

    public function store(array $data): PartnerProjectPayment
    {
        $this->partnerProjectPayment = PartnerProjectPayment::create($data);

        return $this->partnerProjectPayment;
    }

    public function update(array $data): PartnerProjectPayment
    {
        $this->partnerProjectPayment->update($data);

        return $this->partnerProjectPayment->refresh();
    }
}
