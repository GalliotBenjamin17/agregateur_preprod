<x-app-layout>
    <x-slot:content>
        <x-layouts.card :name="$project->name" :thin-padding="false">
            <x-slot:groupName>
                @if ($project->hasParent())
                    Sous-projet
                @else
                    Projet
                @endif
            </x-slot:groupName>

            <x-slot:icon>
                {!! \App\Helpers\IconHelper::projectIcon(size: 'lg') !!}
            </x-slot:icon>

            <x-slot:actions>
                <div class="flex items-center space-x-2">
                    <x-button icon href="{{ route('projects.show.map', ['project' => $project]) }}">
                        <x-icon.carte class="h-5 text-gray-400 w-5" />
                        <span class="text-gray-700">Carte</span>
                    </x-button>
                    <x-reminder-widget :model="$project" />
                    @if ($donationSplitsCount == 0)
                        <x-button type="danger" data-bs-toggle="modal" data-bs-target="#delete_project">
                            Supprimer
                        </x-button>
                    @endif
                </div>
            </x-slot:actions>

            @section('title', $project->name)

            <x-layouts.card-content-attributes>
                <x-layouts.card-content-attribute label="Instance" :value="$project->tenant?->name ?? 'Projet national'" />
                @if ($project->hasParent())
                    <x-layouts.card-content-attribute label="Projet parent">
                        <x-slot:value>
                            <a class="link"
                                href="{{ route('projects.show.goals', ['project' => $project->parentProject->slug]) }}">
                                <span>{{ $project->parentProject?->name ?? 'Projet inconnu' }}</span>
                            </a>
                        </x-slot:value>
                    </x-layouts.card-content-attribute>
                @endif
                <x-layouts.card-content-attribute label="Porteur">
                    <x-slot:value>
                        <a class="link"
                            href="{{ $project->sponsor ? (method_exists($project->sponsor, 'redirectRouter') ? $project->sponsor->redirectRouter() : '#!') : '#!' }}">
                            <span>{{ $project->sponsor?->name ?? 'Sponsor inconnu' }}</span>
                        </a>
                    </x-slot:value>
                </x-layouts.card-content-attribute>
                <x-layouts.card-content-attribute label="Certification" :value="$project->certification?->name ??
                    ($project->parentProject->certification?->name ?? 'Certification non-définie')" />
                <x-layouts.card-content-attribute label="Création" :value="\Carbon\Carbon::userDatetime($project->created_at, capitalized: true)" />
            </x-layouts.card-content-attributes>

        </x-layouts.card>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3">

            <div class="col-span-12">
                {{-- MENU PROJET --}}
                @include('app.projects.details.layouts.partials.project-menu', ['project' => $project])
            </div>
            <div @class([
                'md:col-span-8' => isset($cardContent),
                'md:col-span-12' => isset($fullContent),
            ])>
                <div class="bg-white rounded-md border border-gray-300 p-2.5 sm:p-[1rem]">
                    <div class="mt-0 space-y-3">
                        @isset($cardContent)
                            {{ $cardContent }}
                        @endisset
                        @isset($fullContent)
                            {{ $fullContent }}
                        @endisset
                    </div>
                </div>
            </div>

            @if (!isset($fullContent))
                <div class="md:col-span-4">
                    <div class="space-y-2 sm:space-y-3">
                        @if ($project->certification_id)
                            <x-layouts.card group-name="Projet" name="Informations importantes" :thin-padding="true">
                                <x-slot:icon>
                                    {!! \App\Helpers\IconHelper::projectIcon(size: 'lg') !!}
                                </x-slot:icon>

                                <x-slot:content>
                                    <div class="p-2.5 sm:p-[1rem] flex items-center flex-wrap gap-x-5 gap-y-2">
                                        @if ($project->certification)
                                            <img class="h-12 tippy" data-tippy-content="Certification"
                                                src="{{ asset($project->certification->image) }}">
                                        @endif
                                    </div>
                                </x-slot:content>
                            </x-layouts.card>
                        @endif

                        @if (!$project->hasParent())
                            <x-odd-component :project="$project" />
                        @endif

                        <x-comments-card :model="$project" />
                        <x-activities-model :model="$project" />
                    </div>

                    @isset($colContent)
                        {{ $colContent }}
                    @endisset
                </div>
            @endif
        </div>
    </x-slot:content>

    <x-slot:modals>
        @if ($donationSplitsCount == 0)
            <x-modal id="delete_project">
                <x-modal.header class="bg-red-500">
                    <div class="font-semibold  text-white">
                        @if ($project->hasParent())
                            Suppression du sous-projet
                        @else
                            Suppression du projet
                        @endif
                    </div>
                    <x-modal.close-button class="text-white" />
                </x-modal.header>

                <form autocomplete="off" action="{{ route('projects.delete', ['project' => $project]) }}" method="POST"
                    enctype='multipart/form-data'>
                    @csrf
                    <x-modal.body class="border-t border-gray-300">
                        Cette action est irréversible et supprimera toutes les informations liées à ce projet.
                    </x-modal.body>

                    <x-modal.footer>
                        <x-button submit type="dangerous">
                            Supprimer
                        </x-button>
                        <x-button data-bs-dismiss="modal">
                            Fermer
                        </x-button>
                    </x-modal.footer>
                </form>
            </x-modal>
        @endif

        @isset($modals)
            {{ $modals }}
        @endisset
    </x-slot:modals>
</x-app-layout>