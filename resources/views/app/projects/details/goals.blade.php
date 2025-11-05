<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">
        <div class="space-y-5">
            @if(!$project->hasParent())
                <x-layouts.card
                    name="Sous-projets"
                    :thin-padding="true"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::projectIcon(size: 'sm') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        @if($project->hasFundingAdded())
                            @role('admin|local_admin')
                                <div class="flex items-center space-x-3">
                                    <x-button type="default" data-bs-toggle="modal" data-bs-target="#add_sub_project">
                                        Ajouter un sous-projet
                                    </x-button>
                                </div>
                            @endrole
                        @else
                            <div class="border-l-4 border-yellow-400 bg-yellow-50 px-4 py-1 rounded-md">
                                <p class="text-sm text-yellow-700">
                                    Vous devez renseigner les informations de <a href="{{ route('projects.show.costs', ['project' => $project]) }}" class="italic hover:underline">financement</a> pour ajouter un sous-projet
                                </p>
                            </div>
                        @endif
                    </x-slot:actions>

<x-slot:content>
                        <livewire:tables.projects.index-table :project="$project" />
                    </x-slot:content>
                </x-layouts.card>
            @endif

            
        </div>
    </x-slot>

    <x-slot:modals>
        @if($project->hasFundingAdded() and !$project->hasParent())
            <x-modal size="lg" id="add_sub_project">
                <x-modal.header>
                    <div>
                        <div class="font-semibold text-gray-700">
                            Ajout d'un sous-projet
                        </div>
                    </div>
                    <x-modal.close-button/>
                </x-modal.header>

                <x-modal.body class="border-t border-gray-300 space-y-5">
                    <x-information-alert
                        type="info"
                        title="Détails des types de sous-projets"
                        message="Vous pouvez ajouter un sous-projet temporel correspondant à une segmentation par année. Un sous-projet géographique est un segmentation sur un lieu spécifique."
                    />
                    <livewire:forms.sub-project-create-form :project="$project" />
                </x-modal.body>
            </x-modal>
        @endif
    </x-slot:modals>
</x-pages.projects.details-base>

