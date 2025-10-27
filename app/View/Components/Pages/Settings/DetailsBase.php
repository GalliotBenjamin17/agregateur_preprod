<?php

namespace App\View\Components\Pages\Settings;

use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public string $pageName
    ) {
    }

    public function render(): View
    {
        return view('app.settings.layouts.base');
    }
}
