<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">

        <livewire:infolists.projects.financial-exports-infolist
            :project="$project"
        />

    </x-slot>
</x-pages.projects.details-base>
