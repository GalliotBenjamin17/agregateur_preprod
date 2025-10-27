<x-pages.settings.details-base
    page-name="Types d'événements"
>
    <x-slot:actions>
        <x-button href="#!" data-bs-toggle="modal" data-bs-target="#add_event_type">
            Ajouter un type d'événement
        </x-button>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div class="mt-5 rounded-md overflow-hidden">
            <livewire:tables.event-types-table />
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modal id="add_event_type">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'un type d'événement
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('event-types.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <x-label for="name" value="Nom" required/>
                        <x-input id="name" placeholder="Inauguration" type="text" name="name" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
