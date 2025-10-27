<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">

        <livewire:actions.projects.generate-annual-report
            :project="$project"
        />

        <livewire:forms.projects.contributors-form :project="$project" />

    </x-slot>
</x-pages.projects.details-base>
