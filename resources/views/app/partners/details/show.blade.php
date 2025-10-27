<x-pages.partners.details-base
    :partner="$partner"
>
    <x-slot name="cardContent">
        <livewire:forms.partners.details-form :partner="$partner" />
    </x-slot>
</x-pages.partners.details-base>
