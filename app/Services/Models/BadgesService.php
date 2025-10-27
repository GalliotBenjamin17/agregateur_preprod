<?php

namespace App\Services\Models;

use App\Models\Badge;

class BadgesService
{
    public function __construct(
        public ?Badge $badge = null
    ) {
    }

    public function store(array $data): Badge
    {
        $this->badge = Badge::create($data);

        return $this->badge;
    }

    public function update(array $data): Badge
    {
        $this->badge->update($data);

        return $this->badge->refresh();
    }
}
