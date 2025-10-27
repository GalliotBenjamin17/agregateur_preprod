<?php

namespace App\Services\Models;

use App\Enums\Roles;
use App\Models\Organization;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Arr;

class UserService
{
    public function storeUser(array $data, bool $isRegister = false): User
    {
        $user = User::create($data);

        if (Arr::get($data, 'organization_id')) {
            $organization = Organization::findOrFail($data['organization_id']);

            $organization->users()->attach($user);
            $user->assignRole(Roles::Member);

            $user->sendWelcomeNotification(now()->addYear());

            return $user;
        }

        if (Arr::get($data, 'partner_id')) {
            $partner = Partner::findOrFail($data['partner_id']);

            $partner->users()->attach($user);
            $user->assignRole(Roles::Partner);

            $user->sendWelcomeNotification(now()->addYear());

            return $user;
        }

        if (Arr::get($data, 'role') != Roles::Admin) {
            $user->assignRole($data['role']);
            $user->sendWelcomeNotification(validUntil: now()->addYear(), isMigration: false, isRegister: $isRegister);

            return $user;
        }

        // Do admin logic here
        $user->assignRole(Roles::Admin);
        try {
            $user->sendWelcomeNotification(now()->addYear());
        } catch (\Exception $e) {
            \Log::alert($e->getMessage());
            \Session::flash('alert', "Nous n'avons pu envoyer l'email d'inscription mais l'utilisateur a été ajouté à la plateforme.");
        }

        return $user;
    }
}
