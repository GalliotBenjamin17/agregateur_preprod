<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">

        <div class="">
            <livewire:forms.projects.project-relations-form :project="$project" />
        </div>

        <x-files-component :model="$project" />
    </x-slot>
</x-pages.projects.details-base>
