<x-app-contributors-2
    :tenant="$tenant"
    :organization="$organization"
>

    @if($organization)
        <div class="flex items-center justify-between">
            <x-contributors-space.section-title
                :title="$organization->name"
                class="py-5 font-semibold "
            />
        </div>

    @else
        <div class="flex items-center justify-between">

            <x-contributors-space.section-title
                title="Informations personnelles"
                class="py-5 font-semibold "
            />

            <livewire:actions.users.update-password :user="request()->user()" />

        </div>
    @endif

    @if($organization)
       <livewire:interface.forms.profile.organization-profile-form :organization="$organization" />
    @else
        <livewire:interface.forms.profile.user-profile-form />
    @endif


    @if($isManager)
        <div class="mt-10">

            <div class="flex items-center justify-between">

                <x-contributors-space.section-title
                    title="Utilisateurs"
                    class="py-10 font-semibold "
                />

                <x-button data-bs-toggle="modal" data-bs-target="#add_user">
                    Ajouter un utilisateur
                </x-button>

                <x-modals.create-user
                    :organization="$organization"
                />
            </div>


            <div class="rounded-md overflow-hidden border border-gray-300">
                <livewire:tables.organizations.users-table :organization="$organization" :compact="true" />
            </div>
        </div>
    @endif
</x-app-contributors-2>
