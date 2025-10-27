<?php

namespace App\Services\Models;

use App\Models\Partner;

class PartnersService
{
    public function __construct(
        public ?Partner $partner = null
    ) {
    }

    public function store(array $data): Partner
    {
        $this->partner = Partner::create($data);

        return $this->partner;
    }

    public function update(array $data): Partner
    {
        $this->partner->update($data);

        return $this->partner->refresh();
    }
}
