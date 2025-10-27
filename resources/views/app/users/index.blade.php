<x-app-layout>
    <x-slot name="content">
        <x-layouts.card
            group-name="Acteurs"
            name="Tous les acteurs"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::usersIcon() !!}
            </x-slot:icon>

            <x-slot:actions>
                <livewire:actions.users.sync-users />
                <x-button data-bs-toggle="modal" data-bs-target="#add_user" size="sm" type="default">
                    Ajouter un utilisateur
                </x-button>
            </x-slot:actions>

            <x-slot:content>
                @section('title', "Acteurs")

                <livewire:tables.users.index-table />
            </x-slot:content>
        </x-layouts.card>
    </x-slot>

    <x-slot:modals>
        <x-modals.create-user />
    </x-slot:modals>
</x-app-layout>
