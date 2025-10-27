<x-pages.settings.details-base
    page-name="Nuances politiques"
>
    <x-slot:actions>
        <x-button data-bs-toggle="modal" data-bs-target="#add_nuance">
            Ajouter une nuance
        </x-button>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div class="mt-5 rounded-md overflow-hidden">
            <livewire:tables.nuances-table />
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modal id="add_nuance">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'une nuance
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('nuances.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                    <div>
                        <x-label value="Nom : " required />
                        <x-input type="text" name="name" placeholder="Divers" />
                    </div>
                    <div>
                        <x-label value="Groupe : " required />
                        <x-input type="text" name="global_key" placeholder="Divers" />
                    </div>
                    <div>
                        <x-label value="Clé : " required />
                        <x-input type="text" name="key" placeholder="DVS" />
                    </div>
                    <div>
                        <x-label value="Couleur : " required />
                        <x-input type="color" class="py-1" name="color" placeholder="Divers" />
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
