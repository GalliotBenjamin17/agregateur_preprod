<?php

namespace App\Traits\Filament;

use Closure;

trait HasRing
{
    protected int|Closure|null $ring = null;

    public function ring(string|Closure|null $ring): static
    {
        $this->ring = $ring;

        return $this;
    }

    public function getRing(): ?int
    {
        return $this->evaluate($this->ring);
    }
}
