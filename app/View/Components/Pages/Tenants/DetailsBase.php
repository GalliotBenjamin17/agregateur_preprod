<?php

namespace App\View\Components\Pages\Tenants;

use App\Models\Tenant;
use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public Tenant $tenant
    ) {
        $this->tenant->load([
            'createdBy',
        ]);
    }

    public function render(): View
    {
        return view('app.tenants.details.layouts.base');
    }
}
