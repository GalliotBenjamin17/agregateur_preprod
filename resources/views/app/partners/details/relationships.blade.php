<x-pages.partners.details-base
    :partner="$partner"
>
    <x-slot name="cardContent">
        <x-files-component :model="$partner" />
    </x-slot>
</x-pages.partners.details-base>
