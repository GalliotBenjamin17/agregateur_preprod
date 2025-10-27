<x-pages.settings.details-base
    page-name="Templates emails"
>
    <x-slot:actions>
        <x-button data-bs-toggle="modal" data-bs-target="#add_template">
            Ajouter un template
        </x-button>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div class="mt-5 rounded-md overflow-hidden">
            <livewire:tables.emails-templates-table />
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modal id="add_template">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout un template email
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('settings.emails-templates.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                    <div class="sm:col-span-2">
                        <x-label value="Nom : " required />
                        <x-input type="text" name="name" required placeholder="Newsletter #2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-label value="Description : " />
                        <x-textarea type="text" name="description" placeholder="Mise en avant auprès des élus"></x-textarea>
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
