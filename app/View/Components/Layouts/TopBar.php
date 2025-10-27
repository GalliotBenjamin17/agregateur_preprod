<?php

namespace App\View\Components\Layouts;

use App\Models\Tenant;
use Illuminate\View\Component;

class TopBar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public ?string $logo = null;

    public function __construct()
    {
        $this->logo = match (userHasTenant()) {
            true => Tenant::find(userTenantId())->logo,
            false => '/img/logos/cooperative-carbone/main.svg'
        };
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.layouts.top-bar');
    }
}
