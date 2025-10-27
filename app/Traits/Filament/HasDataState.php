<?php

namespace App\Traits\Filament;

trait HasDataState
{
    public ?array $data = [];

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }
}
