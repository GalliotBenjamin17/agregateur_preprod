<form wire:submit="submit">
    {{ $this->form }}

    @if($showSubmit)
        @role('admin|local_admin')
            <div class="pt-5 mt-5 border-t border-gray-300 flex justify-end">
                <x-button submit type="success" size="lg">
                    Mettre à jour
                </x-button>
            </div>
        @endrole
    @endif

</form>

