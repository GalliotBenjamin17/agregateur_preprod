<x-pages.users.details-base
    :user="$user"
>
    <x-slot name="cardContent">
        <livewire:forms.users.profile-informations-form :user="$user" />
    </x-slot>

    <x-slot name="colContent">
        <div class="space-y-2 sm:space-y-3">
            <x-activities-model :model="$user" />
        </div>
    </x-slot>

</x-pages.users.details-base>
