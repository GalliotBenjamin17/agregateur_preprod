<form wire:submit="submit">
    {{ $this->form }}

    @role('admin|local_admin')
        @if(!$project->hasFormFieldsDisabled())
            <div class="pt-5 mt-5 border-t border-gray-300 flex justify-end">
                <x-button submit type="success" size="lg">
                    Mettre à jour
                </x-button>
            </div>
        @endif
    @endrole
</form>
