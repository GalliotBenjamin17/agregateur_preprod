<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="cardContent">
        <livewire:forms.projects.details-form :project="$project" />
    </x-slot>
</x-pages.projects.details-base>
