<?php

namespace App\View\Components\Pages\Profile;

use App\Models\Tenant;
use Illuminate\View\Component;

class DetailsBase extends Component
{
    public function __construct(
        public string $pageName
    ) {
    }

    public function render()
    {
        return view('app.profile.layouts.base', [
            'tenant' => match ((bool) request()->subdomain()) {
                true => Tenant::where('domain', request()->subdomain())->firstOrFail(),
                false => null
            },
        ]);
    }
}
