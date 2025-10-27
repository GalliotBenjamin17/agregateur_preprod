<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;

trait HasAddress
{
    /**
     * Generate an address string.
     *
     * @return void
     */
    public function address(): Attribute
    {
        return Attribute::get(function () {
            return Arr::join(collect([$this->address_1, $this->address_2, $this->address_postal_code, $this->address_city])->whereNotNull()->toArray(), ', ', ', ');
        });
    }
}
