<?php

namespace App\View\Components;

use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TenantForm extends Component
{
    public function __construct(
        public string $name = 'tenant_id'
    ) {
    }

    public function render(): View
    {
        return view('components.tenant-form', [
            'tenants' => Tenant::orderBy('name')->get(),
        ]);
    }
}
