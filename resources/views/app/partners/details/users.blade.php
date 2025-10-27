<x-pages.partners.details-base
    :partner="$partner"
>
    <x-slot name="cardContent">
        <x-layouts.card
            name="Utilisateurs ({{ $usersCount }})"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::usersIcon(size: 'sm') !!}
            </x-slot:icon>

            <x-slot:actions>
                <x-button data-bs-toggle="modal" data-bs-target="#add_user">
                    Ajouter un utilisateur
                </x-button>
                <livewire:actions.partners.link-users :partner="$partner" />
            </x-slot:actions>

            <x-slot:content>
                @if($usersCount > 0)
                    <livewire:tables.partners.users-table :partner="$partner" />
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
            :partner="$partner"
        />
    </x-slot:modals>
</x-pages.partners.details-base>
