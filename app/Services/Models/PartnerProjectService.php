<?php

namespace App\Services\Models;

use App\Models\PartnerProject;

class PartnerProjectService
{
    public function __construct(
        public ?PartnerProject $partnerProject = null
    ) {
    }

    public function store(array $data): PartnerProject
    {
        $this->partnerProject = PartnerProject::create($data);

        return $this->partnerProject;
    }

    public function update(array $data): PartnerProject
    {
        $this->partnerProject->update($data);

        return $this->partnerProject->refresh();
    }
}
