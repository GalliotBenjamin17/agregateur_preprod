<x-menu-card-group>
    <x-menu-tab content="Projets" :with-submenu="true" :is-active="request()->routeIs(
        'projects.show.details',
        'projects.show.goals',
        'projects.show.methods-informations',
    )">
        <x-menu-card :url="route('projects.show.details', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.details')" title="Fiche projet"
            description="Détails du projet" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::projectIcon() !!}
            </x-slot:icon>
        </x-menu-card>

        <x-menu-card :url="route('projects.show.goals', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.goals')" title="Sous-projets"
            description="Gestion des sous-projets" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::projectIcon() !!}
            </x-slot:icon>
        </x-menu-card>

        <x-menu-card :url="route('projects.show.methods-informations', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.methods-informations')" title="Labellisation"
            description="Processus de labélisation" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::projectIcon() !!}
            </x-slot:icon>
        </x-menu-card>
    </x-menu-tab>

    <x-menu-tab content="Parties prenantes" :with-submenu="true" :is-active="request()->routeIs('projects.show.partners*', 'projects.show')">
        <x-menu-card :url="route('projects.show.partners', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.partners*')" title="Partenaires"
            description="Gestion des partenaires du projet" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::partnersIcon() !!}
            </x-slot:icon>
        </x-menu-card>

        <x-menu-card :url="route('projects.show', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show')" title="Relations"
            description="Auditeurs / référant / porteur / fichiers" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::usersIcon() !!}
            </x-slot:icon>
        </x-menu-card>
    </x-menu-tab>

    <x-menu-tab content="Finances" :with-submenu="true" :is-active="request()->routeIs(
        'projects.show.financial-exports',
        'projects.show.costs',
        'projects.show.donations',
    )">
        <x-menu-card :url="route('projects.show.donations', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.donations')" title="Contribution"
            description="Toutes les contributions" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::transactionsIcon() !!}
            </x-slot:icon>
        </x-menu-card>
        <x-menu-card :url="route('projects.show.costs', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.costs')" title="Comptabilité"
            description="Informations de financement / Paiements initiés / Prix tonnes / Dépenses - Recettes "
            wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::donationsIcon() !!}
            </x-slot:icon>
        </x-menu-card>
        <x-menu-card :url="route('projects.show.financial-exports', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.financial-exports')" title="Financement"
            description="Bilans et audits par années" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::transactionsIcon() !!}
            </x-slot:icon>
        </x-menu-card>
    </x-menu-tab>

    <x-menu-tab content="Communication" :with-submenu="true" :is-active="request()->routeIs('projects.show.news', 'projects.show.contributors')">
        <x-menu-card :url="route('projects.show.news', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.news')" title="Actualités"
            description="Actualités externes du projet (sur site web)" wire:navigate.hover>
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::newsIcon() !!}
            </x-slot:icon>
        </x-menu-card>

        @role('admin|local_admin')
            <x-menu-card :url="route('projects.show.contributors', ['project' => $project->slug])" :is-active="request()->routeIs('projects.show.contributors')" title="Interface contributeur"
                description="Gestion rapport annuel / documents additionels" wire:navigate.hover>
                <x-slot:icon>
                    {!! \App\Helpers\IconHelper::usersIcon() !!}
                </x-slot:icon>
            </x-menu-card>
        @endrole
    </x-menu-tab>
</x-menu-card-group>