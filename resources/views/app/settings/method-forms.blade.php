<x-pages.settings.details-base
    page-name="Méthodes"
>
    <x-slot:actions>
        <x-button data-bs-toggle="modal" data-bs-target="#add_method_form_group">
            Ajouter une méthode
        </x-button>
    </x-slot:actions>

    <x-slot name="cardContent">
        <div class="mt-5">
            <livewire:tables.settings.method-form-groups-table />
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modal id="add_method_form_group">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'une méthode
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('method-form-groups.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-1 gap-4 border-t border-gray-300">
                    <div>
                        <x-label value="Nom de la méthode : " required />
                        <x-input type="text" name="name" placeholder="Divers" required />
                    </div>

                    <div>
                        <x-label value="Description : " />
                        <x-textarea type="text" name="description"></x-textarea>
                    </div>

                    <div>
                        <x-label value="Segmentation : " required />
                        <x-select required name="segmentation_id">
                            @foreach($segmentations as $segmentation)
                                <option value="{{ $segmentation->id }}">{{ $segmentation->name }}</option>
                            @endforeach
                        </x-select>
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
