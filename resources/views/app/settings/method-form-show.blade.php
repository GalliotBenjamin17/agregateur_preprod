<x-pages.settings.details-base
    :page-name="$methodForm->name"
>
    <x-slot:actions>
        <x-button href="{{ route('settings.method-form-groups.show', ['methodFormGroup' => $methodForm->methodFormGroup]) }}">
            Liste des versions
        </x-button>
    </x-slot:actions>
    <x-slot name="cardContent">
        <style>
            .p-6 {
                padding: 1rem;
            }
        </style>
        <div class="mt-5">
            <livewire:forms.settings.method-form-form :method-form="$methodForm" />
        </div>
    </x-slot>
</x-pages.settings.details-base>
