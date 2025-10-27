<x-pages.settings.details-base
    page-name="Antennes locales"
>
    <x-slot:actions>
        <x-button href="{{ route('settings.index.tenants') }}">
            Toutes les instances
        </x-button>
    </x-slot:actions>
    <x-slot name="cardContent">
        <div class="mt-5">
            <livewire:forms.tenants.details-form :tenant="$tenant" />
        </div>
    </x-slot>
</x-pages.settings.details-base>
