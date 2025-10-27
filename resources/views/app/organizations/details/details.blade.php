<x-pages.organizations.details-base
    :organization="$organization"
>
    <x-slot name="cardContent">
        <livewire:forms.organizations.details-form :organization="$organization" />
    </x-slot>
</x-pages.organizations.details-base>
