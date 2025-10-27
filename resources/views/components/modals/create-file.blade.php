@props([
    'id' => 'add_file',
    'model' => null
])

<x-modal id="{{ $id }}">
    <x-modal.header>
        <div>
            <!-- sloboda studio -->
            <div class="font-semibold text-gray-700">
                Ajouter un fichier
            </div>
        </div>
        <x-modal.close-button/>
    </x-modal.header>

    <form autocomplete="off" action="{{ route('files.store') }}" method="POST" enctype='multipart/form-data'>
        @csrf
        <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300" x-data="{fileName: ''}">
            @if($model)
                <div class="sm:col-span-2">
                    <x-label value="Relié à :" />
                    <div class="py-2">
                        <div class="flex items-center space-x-3">
                            <span>{!! \App\Helpers\IconHelper::viewIcon(model: $model, size: "xs") !!}</span>
                            <div class="leading-tight">
                                <h3 class="text-[14px]">{{ $model->name ?? $model->title }}</h3>
                            </div>
                        </div>
                    </div>
                    <input class="hidden" name="model_id" value="{{ $model->id }}">
                    <input class="hidden" name="model_class" value="{{ get_class($model) }}">
                </div>
            @endif

             <div class="sm:col-span-2">
                <div class="flex items-start space-x-7">
                    <div>
                        <x-label value="Fichier : " required />
                        <label for="logo" class="inline-flex mt-1.5">
                            <x-button class="">
                                Charger le fichier
                            </x-button>
                        </label>
                        <span class="ml-2 text-sm text-gray-500 " x-text="fileName"></span>
                        <input type="file" class="hidden" name="file" x-ref="file" @change="fileName = $refs.file.files[0].name"
                               id="logo" required>
                    </div>
                </div>
            </div>

            <div class="sm:col-span-2">
                <x-label value="Nom du fichier" />
                <x-input type="text" name="name" x-bind:value="fileName" placeholder="Fiche explicative" required />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-button data-bs-dismiss="modal">
                Fermer
            </x-button>
            <x-button submit type="success">
                Ajouter
            </x-button>
        </x-modal.footer>
    </form>
</x-modal>
