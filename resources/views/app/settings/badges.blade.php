<x-pages.settings.details-base
    page-name="Badges"
>
    <x-slot name="actions">
        <livewire:actions.badges.create-form />
    </x-slot>

    <x-slot name="cardContent">
        <div class="mt-5">

            <livewire:tables.badges.index-table />

        </div>
    </x-slot>
</x-pages.settings.details-base>
