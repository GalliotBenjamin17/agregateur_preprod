<?php

namespace App\View\Components\Pages\Organizations;

use App\Models\Organization;
use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public Organization $organization
    ) {
        $this->organization->load([
            'organizationType',
        ]);
    }

    public function render(): View
    {
        return view('app.organizations.details.layouts.base');
    }
}
