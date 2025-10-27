<x-pages.organizations.details-base
    :organization="$organization"
>
    <x-slot name="cardContent">
        <x-layouts.card
            name="Projets ({{ sizeof($organization->projects) }})"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::projectIcon(size: 'sm') !!}
            </x-slot:icon>

            <x-slot:content>
                @if(sizeof($organization->projects))
                    <livewire:tables.projects.index-table :organization="$organization" />
                @else
                    <div class="p-2.5 sm:p-[1rem] grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-4">
                        <x-empty-model
                            content="Aucun projet sur cette organisation"
                            :model="new \App\Models\Project()"
                            class="col-span-4"
                            height="48"
                        />
                    </div>
                @endif
            </x-slot:content>
        </x-layouts.card>
    </x-slot>
</x-pages.organizations.details-base>
