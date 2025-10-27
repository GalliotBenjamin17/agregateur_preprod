<form wire:submit="submit">
    <div class="p-5">
        {{ $this->form }}
    </div>

    <div class="p-3 mt-5 border-t border-gray-300 flex justify-end">
        <x-button submit type="success" size="lg">
            Mettre à jour
        </x-button>
    </div>


</form>
