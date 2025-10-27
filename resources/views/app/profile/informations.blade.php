<x-pages.profile.details-base
    page-name="Informations"
>
    <x-slot name="cardContent">
        <div class="mt-5">
            <livewire:forms.users.profile-informations-form :user="$user" />
        </div>
    </x-slot>
</x-pages.profile.details-base>
