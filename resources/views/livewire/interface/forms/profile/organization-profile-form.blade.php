<form wire:submit="submit">

    {{ $this->form }}

    @if($canUpdateForm)
        <div class="pt-5 mt-10 border-t border-gray-300 flex justify-end">
            <x-button submit type="success" size="lg">
                Mettre à jour
            </x-button>
        </div>
    @endif

</form>
