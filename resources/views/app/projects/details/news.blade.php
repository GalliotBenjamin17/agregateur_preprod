<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">
        @role('admin|local_admin')
            <div class="flex justify-end">
                <livewire:actions.news.create-form :project="$project" />
            </div>
        @endrole

        <livewire:tables.news.index-table :project="$project" />
    </x-slot>
</x-pages.projects.details-base>
