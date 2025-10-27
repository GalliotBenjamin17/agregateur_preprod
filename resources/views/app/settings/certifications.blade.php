<x-pages.settings.details-base
    page-name="Certifications"
>
    <x-slot:actions>
        <x-button data-bs-toggle="modal" data-bs-target="#add_certification">
            Ajouter une certification
        </x-button>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div class="mt-5 ">
            <livewire:tables.settings.certifications-table />
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modal id="add_certification">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'une certification
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('certifications.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-1 gap-4 border-t border-gray-300">
                    <div>
                        <x-label value="Nom : " required />
                        <x-input type="text" name="name" placeholder="Divers" required />
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
