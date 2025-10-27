<div>
    <form wire:submit="submit">
        {{ $this->form }}

        @role('admin|local_admin')
            <div class="pt-5 mt-5 border-t border-gray-300 flex space-x-2 justify-end">
                <x-button type="danger"  size="lg" icon data-bs-toggle="modal" data-bs-target="#delete_method">
                    <x-icon.poubelle class="h-5 w-5" />
                </x-button>

                <x-button submit type="success" size="lg">
                    Mettre à jour
                </x-button>
            </div>
        @endrole
    </form>

    @role('admin|local_admin')

        <x-modal id="delete_method">
            <x-modal.header class="bg-red-500">
                <div class="font-semibold  text-white">
                    Supprimer la méthode sur ce projet
                </div>
                <x-modal.close-button class="text-white"/>
            </x-modal.header>

            <form wire:submit="resetMethodProject">
                @csrf
                <x-modal.body class="border-t border-gray-300">
                    Cette action est irréversible et supprimera toutes les informations liées à cette méthode.
                </x-modal.body>

                <x-modal.footer>
                    <x-button submit type="dangerous">
                        Supprimer
                    </x-button>
                    <x-button data-bs-dismiss="modal">
                        Fermer
                    </x-button>
                </x-modal.footer>
            </form>
        </x-modal>
    @endrole
</div>
