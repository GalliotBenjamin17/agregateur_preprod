<x-pages.settings.details-base
    page-name="Administrateurs"
>
    <x-slot:actions>
        <div class="flex items-center space-x-1">
            @role(\App\Enums\Roles::Admin)
                <x-button icon href="#!" data-bs-toggle="modal" data-bs-target="#add_admin_natio">
                    <x-icon.plus class="h-5 w-5" />
                    <span>Administrateur national</span>
                </x-button>
            @endrole
            <x-button icon href="#!" data-bs-toggle="modal" data-bs-target="#add_admin_local">
                <x-icon.plus class="h-5 w-5" />
                <span>Administrateur local</span>
            </x-button>
        </div>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div class="mt-5 ">
            <livewire:tables.settings.admins-table />
        </div>
    </x-slot>

    <x-slot:modals>
        @role(\App\Enums\Roles::Admin)
            <x-modals.create-user
                id="add_admin_natio"
                title="Ajouter un administrateur"
                :role="\App\Enums\Roles::Admin"
            />
        @endrole
        <x-modals.create-user
            id="add_admin_local"
            title="Ajouter un administrateur"
            :role="\App\Enums\Roles::LocalAdmin"
        />
    </x-slot:modals>
</x-pages.settings.details-base>
