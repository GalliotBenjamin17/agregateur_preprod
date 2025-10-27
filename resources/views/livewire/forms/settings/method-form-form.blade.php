<div>
    <form wire:submit="submit">
        {{ $this->form }}

        <div class="pt-5 mt-5 border-t border-gray-300 flex justify-end space-x-2">
            <x-button href="#!" type="button" type="info" size="lg" data-bs-toggle="modal" data-bs-target="#preview">
                Prévisualiser
            </x-button>

            @if(is_null($methodForm->locked_at))
                <x-button href="#!" type="warning" size="lg" data-bs-toggle="modal" data-bs-target="#lock_method_form">
                    Bloquer la méthode
                </x-button>

                <x-button submit type="success" size="lg">
                    Mettre à jour
                </x-button>
            @endif
        </div>
    </form>

    <x-modal id="lock_method_form">
        <x-modal.header class="bg-yellow-500">
            <div class="font-semibold text-black">
                Bloquer la modification de la méthode
            </div>
            <x-modal.close-button class="text-white"/>
        </x-modal.header>

        <form autocomplete="off" wire:submit="lockMethodForm" >
            @csrf
            <x-modal.body class="border-t border-gray-300">
                En bloquant la modification de cette version, vous permettez à celle-ci d'être affiliée comme la version active de la méthode.
                <br><br>
                Cette action est irréversible et bloquera la modification future de cette version.
            </x-modal.body>

            <x-modal.footer>
                <x-button submit type="warning">
                    Bloquer
                </x-button>
                <x-button data-bs-dismiss="modal">
                    Fermer
                </x-button>
            </x-modal.footer>
        </form>
    </x-modal>

    <x-modal id="preview" size="xl">
        <x-modal.header>
            <div class="font-semibold text-black">
                Prévisualiser la méthode
            </div>
            <x-modal.close-button class="text-white"/>
        </x-modal.header>

        <x-modal.body>
            <livewire:forms.method-form-preview-form :method-form="$methodForm" />
        </x-modal.body>
    </x-modal>
</div>
