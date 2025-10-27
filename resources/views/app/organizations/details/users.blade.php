<x-pages.organizations.details-base
    :organization="$organization"
>
    <x-slot name="cardContent">
        <x-layouts.card
            name="Utilisateurs ({{ $organization->users_count }})"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::usersIcon(size: 'sm') !!}
            </x-slot:icon>

            <x-slot:actions>
                <x-button data-bs-toggle="modal" data-bs-target="#add_user">
                    Ajouter un utilisateur
                </x-button>
                <livewire:actions.organizations.link-users :organization="$organization" />
            </x-slot:actions>

            <x-slot:content>
                @if($organization->users_count > 0)
                    <livewire:tables.organizations.users-table :organization="$organization" />
                @else
                    <div class="p-2.5 sm:p-[1rem] grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-4">
                        <x-empty-model
                            content="Aucun utilisateur rattaché à cette organisation"
                            :model="new \App\Models\User()"
                            class="col-span-4"
                            height="48"
                        />
                    </div>
                @endif
            </x-slot:content>
        </x-layouts.card>
    </x-slot>

    <x-slot:modals>
        <x-modals.create-user
            :organization="$organization"
        />
    </x-slot:modals>
</x-pages.organizations.details-base>
