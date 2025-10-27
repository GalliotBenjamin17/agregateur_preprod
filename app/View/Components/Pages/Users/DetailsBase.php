<?php

namespace App\View\Components\Pages\Users;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public User $user
    ) {
        $this->user->load([
            'organizations',
            'tenant',
        ]);
    }

    public function render(): View
    {
        return view('app.users.details.layouts.base');
    }
}
