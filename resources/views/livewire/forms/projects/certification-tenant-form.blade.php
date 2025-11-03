<form wire:submit="submit">
    {{ $this->form }}

    @role('admin|local_admin')
        <div class="pt-4 mt-4 border-t border-gray-300 flex justify-end">
            <x-button submit type="success">
                Mettre à jour
            </x-button>
        </div>
    @endrole
</form>

