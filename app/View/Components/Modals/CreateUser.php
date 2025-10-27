<?php

namespace App\View\Components\Modals;

use App\Models\Tenant;
use Illuminate\View\Component;

class CreateUser extends Component
{
    public $currentTenant;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $id = 'add_user',
        public string $title = 'Ajouter un utilisateur',
        public $organization = null,
        public $partner = null,
        public $role = null,
    ) {
        $this->currentTenant = match ((bool) request()->subdomain()) {
            true => Tenant::where('domain', request()->subdomain())->firstOrFail(),
            false => null
        };
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modals.create-user', [
            'tenants' => Tenant::orderBy('name')->get(),
        ]);
    }
}
