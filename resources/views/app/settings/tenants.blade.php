<x-pages.settings.details-base
    page-name="Antennes locales"
>
    <x-slot:actions>
        <x-button data-bs-toggle="modal" data-bs-target="#add_tenant">
            Ajouter une instance
        </x-button>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div class="mt-5">
            <livewire:tables.settings.tenants-table />
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modal id="add_tenant">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'une instance
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('tenants.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                    <div class="sm:col-span-2">
                        <x-label value="Nom : " required />
                        <x-input type="text" name="name" placeholder="Nom de l'instance ..." />
                    </div>

                    <div class="sm:col-span-2">
                        <x-label value="Sous-domaine : " required />
                        <div class="flex items-center space-x-2">
                            <x-input class="flex-grow" size="sm" type="text" name="domain" placeholder="la-rochelle" />
                            <span class="">.{{ config('app.displayed_url') }}</span>
                        </div>
                    </div>
                </x-modal.body>

                <x-modal.footer>
                    <x-button data-bs-dismiss="modal">
                        Fermer
                    </x-button>
                    <x-button submit type="success">
                        Ajouter
                    </x-button>
                </x-modal.footer>
            </form>
        </x-modal>
    </x-slot:modals>
</x-pages.settings.details-base>
