<x-pages.partners.details-base
    :partner="$partner"
>
    <x-slot name="cardContent">

        <livewire:tables.partners.projects-table :partner="$partner" />

    </x-slot>
</x-pages.partners.details-base>
