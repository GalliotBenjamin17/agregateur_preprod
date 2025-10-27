<form wire:submit="submit">

    {{ $this->form }}

    <div class="pt-5 mt-10 border-t border-gray-300 flex justify-end">
        <x-button submit type="success" size="lg">
            Mettre à jour
        </x-button>
    </div>


</form>
