<?php

use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    /**
     * Determine if the operation is being processed asynchronously.
     */
    protected bool $async = false;

    /**
     * The queue that the job will be dispatched to.
     */
    protected string $queue = 'default';

    /**
     * A tag name, that this operation can be filtered by.
     */
    protected ?string $tag = null;

    /**
     * Process the operation.
     */
    public function process(): void
    {
        // $users = \App\Models\User::whereRaw('HOUR(created_at) >= 22 OR HOUR(created_at) < 7')
        //     ->where('created_at', '>', \Carbon\Carbon::make('2024-01-01'))
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        $users = \App\Models\User::all();

        $usersMajuscules = $users->filter(function ($user) {
            $excludedUsers = [
                "Kouabenan Adjoumani Martinaise N'VOUO",
                "JEAN-LUC MARIN",
                "JEAN FRANCOIS MARTINEZ"
            ];

            $fullName = $user->first_name . ' ' . $user->last_name;
            if (in_array($fullName, $excludedUsers)) {
                return false;
            }

            $uppercaseInFirstName = preg_match_all('/[A-Z]/', $user->first_name);
            $uppercaseInLastName = preg_match_all('/[A-Z]/', $user->last_name);

            $firstNameAllUpper = $uppercaseInFirstName == strlen($user->first_name) && strlen($user->first_name) > 0;
            $lastNameAllUpper = $uppercaseInLastName == strlen($user->last_name) && strlen($user->last_name) > 0;

            return ($uppercaseInFirstName >= 3 && $uppercaseInLastName >= 3)
                && !($firstNameAllUpper && $lastNameAllUpper);
        });

        foreach ($usersMajuscules as $usersMajuscule) {
            \Illuminate\Support\Facades\Log::channel('daily')->info($usersMajuscule->name);
        }

        $usersMajuscules->toQuery()->delete();
    }
};
