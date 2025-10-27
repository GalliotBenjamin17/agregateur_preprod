<?php

namespace App\View\Components;

use App\Models\Donation;
use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\View\Component;

class AppContributors2 extends Component
{
    public function __construct(
        public Tenant $tenant,
        public ?Organization $organization = null,
        public bool $blankLayout = false
    ) {
    }

    public function render()
    {
        // Récupérer directement les donation_splits filtrés par utilisateur/organisation
        $donationSplits = \App\Models\DonationSplit::whereHas('donation', function ($query) {
            $query->when($this->organization?->id, function ($q) {
                return $q->where('related_id', $this->organization->id)
                    ->where('related_type', get_class($this->organization));
            }, function ($q) {
                $user = request()->user();
                return $q->where('related_id', $user->id)
                    ->where('related_type', get_class($user));
            });
        }
        )
            ->get();

        // D'abord charger les relations pour pouvoir filtrer
        $donationSplits->load('project.parentProject');

        // Filtrer les splits : si une donation a des splits sur parent ET enfant,
        // ne garder que les splits des enfants
        $filteredSplits = $donationSplits->groupBy('donation_id')->map(function ($splits) {
            // Vérifier s'il y a des splits sur des projets enfants
            $childSplits = $splits->filter(function ($split) {
                return $split->project && $split->project->parentProject !== null;
            });

            // Si il y a des splits sur des enfants, ne garder que ceux-là
            // Sinon garder tous les splits (cas du parent seul)
            return $childSplits->isNotEmpty() ? $childSplits : $splits;
        })->flatten();

        $projectIds = $filteredSplits->pluck('project_id')->unique()->toArray();

        $projects = \App\Models\Project::whereIn('id', $projectIds)->get();

        // Load parentProject and childrenProjects relationships for all projects
        $projects->load('parentProject', 'childrenProjects');

        // Process projects to determine display names based on contribution context
        $processedProjects = $this->processProjectsForDisplay($projects);

        $userOrganizations = request()->user()->organizations()->get();

        return view('layouts.app-contributors-2', [
            'projects' => $processedProjects,
            'userOrganizations' => $userOrganizations,
        ]);
    }

    private function processProjectsForDisplay($projects)
    {
        // Afficher TOUS les projets avec contributions directes
        foreach ($projects as $project) {
            $project->display_name = $project->name;
        }

        return $projects;
    }
}
