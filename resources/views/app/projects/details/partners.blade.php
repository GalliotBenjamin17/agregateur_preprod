<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">
        <div class="flex justify-end">
            <livewire:actions.partners.link-to-project-form :project="$project" />
        </div>

        <livewire:tables.projects.partners-table :project="$project" />
    </x-slot>
</x-pages.projects.details-base>
