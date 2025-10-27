<?php

namespace App\View\Components\Pages\Partners;

use App\Models\Partner;
use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public Partner $partner
    ) {
        $this->partner->load([
            'tenant',
        ]);
    }

    public function render(): View
    {
        return view('app.partners.details.layouts.base');
    }
}
