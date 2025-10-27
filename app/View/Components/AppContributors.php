<?php

namespace App\View\Components;

use App\Models\Tenant;
use Illuminate\View\Component;

class AppContributors extends Component
{
    public function __construct(
        public Tenant $tenant
    ) {
    }

    public function render()
    {
        return view('layouts.app-contributors');
    }
}
