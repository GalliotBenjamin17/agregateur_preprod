<?php

namespace App\Observers\Models;

use App\Enums\Roles;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
    }

    public function updated(User $user): void
    {
        if ($user->roles()->count() == 0) {
            $user->syncRoles(Roles::Subscriber);
        }
    }

    public function deleted(User $user): void
    {
    }

    public function restored(User $user): void
    {
    }

    public function forceDeleted(User $user): void
    {
    }
}
