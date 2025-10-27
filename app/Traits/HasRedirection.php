<?php

namespace App\Traits;

use App\Models\Donation;
use App\Models\File;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;

trait HasRedirection
{
    /**
     * Return the route and parameters of the current model.
     *
     * @return void
     */
    public function redirectRouter(): string
    {
        return match (get_class($this)) {
            File::class => route('files.show', ['file' => $this->slug]),
            User::class => route('users.show.details', ['user' => $this->slug]),
            Project::class => route('projects.show.details', ['project' => $this->slug]),
            Donation::class => route('donations.show', ['donation' => $this->id]),
            Organization::class => route('organizations.show.details', ['organization' => $this->slug]),
            default => route('dashboard')
        };
    }
}
