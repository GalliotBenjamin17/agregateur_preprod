<form wire:submit="submit">
    @if(Session::has('alert'))
        <div class="flex w-full pb-5">
            <x-information-alert
                class="w-full"
                title="Erreur :"
                :message="Session::get('alert')"
                type="danger"
            />
        </div>
    @endif


    {{ $this->form }}

    <div class="pt-2 mt-3 flex justify-end">
        <x-button submit size="lg" type="success">
            Ajouter
        </x-button>
    </div>


</form>
