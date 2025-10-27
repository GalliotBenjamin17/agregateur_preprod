<form wire:submit="submit">
    {{ $this->form }}

    <div class="pt-5 mt-5 border-t border-gray-300 flex justify-between">
        <div class="justify-start">
            <x-button size="lg" type="danger" class="hidden md:inline-flex" href="#" data-bs-toggle="modal" data-bs-target="#delete_user">
                Supprimer
            </x-button>
        </div>
        <div class="justify-end">
            <x-button submit type="success" size="lg">
                Mettre à jour
            </x-button>
        </div>
    </div>


</form>
