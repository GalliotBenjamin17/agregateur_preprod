<x-app-layout>
    <x-slot:content>
        <x-layouts.card
            group-name="Fichiers"
            :name="$file->name"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::filesIcon() !!}
            </x-slot:icon>

            <x-slot:content>
                <x-layouts.card-content-attributes>
                    <x-layouts.card-content-attribute
                        label="Date d'ajout"
                        :value="\Carbon\Carbon::userDatetime($file->created_at, capitalized: true)"
                    />
                    <x-layouts.card-content-attribute
                        label="Ajouté par"
                        :value="$file->createdBy?->name ?? '-'"
                    />
                    @if($file->related)
                        <x-layouts.card-content-attribute
                            label="Relié à"
                        >
                            <x-slot:value>
                                <a class="link" href="{{ method_exists($file->related, 'redirectRouter') ? $file->related->redirectRouter() : '#!' }}">
                                    <span>{{ $file->related?->name ?? 'Supprimé' }}</span>
                                </a>
                            </x-slot:value>
                        </x-layouts.card-content-attribute>
                    @endif
                </x-layouts.card-content-attributes>
            </x-slot:content>

            <x-slot:actions>
                <div class="flex items-center space-x-2">
                    <form method="POST" action="{{ route('files.show.download', ['file' => $file]) }}" >
                        @csrf
                        <x-button icon submit>
                            <span>Télécharger</span>
                            <x-icon.telecharger class="h-4 w-4" />
                        </x-button>
                    </form>
                    <x-button target="_blank" icon href="{{ route('files.show.preview', ['file' => $file]) }}">
                        <span>Ouvrir le fichier</span>
                        <x-icon.lien_externe class="h-4 w-4" />
                    </x-button>
                    @role('admin|local_admin')
                        <x-button icon type="danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_file">
                            <x-icon.poubelle class="h-4 w-4" />
                            <span>Supprimer</span>
                        </x-button>
                    @endrole
                </div>
            </x-slot:actions>
        </x-layouts.card>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="col-span-2">
                @if(in_array($file->extension, ['png','jpe','jpeg','jpg','gif','bmp','ico','tiff','tif','svg','svgz']))
                    <img class="w-full bg-white overflow-hidden rounded-md" src="{{ asset('/storage/' . $file->path) }}">
                @elseif(in_array($file->extension, ['pdf']))
                    <iframe class="w-full h-[900px] bg-white rounded-md overflow-hidden" src="{{ route('files.show.preview', ['file' => $file->slug]) }}"></iframe>
                @else
                    <div class="bg-white rounded-md overflow-hidden p-5">
                        <x-empty-model
                            content="Nous ne pouvons pas afficher <span class='font-bold'>{{ $file->name }}</span> dans le navigateur.<br> Vous devez le télécharger pour le visualiser."
                            :model="new \App\Models\File()"
                            class="col-span-4"
                            height="48"
                        />
                    </div>
                @endif
            </div>
            <div>
                <div class="space-y-3">
                    <x-layouts.card
                        name="Mettre à jour"
                        :thin-padding="true"
                    >
                        <x-slot:content>
                            <div class="p-[1rem]">
                                <livewire:forms.files-form :file="$file" />
                            </div>
                        </x-slot:content>
                    </x-layouts.card>

                    <x-comments-card :model="$file" />
                    <x-activities-model :model="$file" />
                </div>
            </div>
        </div>
    </x-slot:content>

    <x-slot:modals>
        <x-modal id="delete_file">
            <x-modal.header class="bg-red-500">
                <div>
                    <div class="font-semibold text-white">
                        Confirmation de la suppression du fichier
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('files.delete', ['file' => $file]) }}" method="POST" enctype='multipart/form-data'>
                @csrf
                @method('DELETE')
                <x-modal.body class="border-t border-gray-300">
                    <p>
                        Vous vous apprêtez à supprimer ce fichier. Cette action est irréversible !
                    </p>
                </x-modal.body>

                <x-modal.footer>
                    <x-button submit type="dangerous">
                        Supprimer le fichier
                    </x-button>
                    <x-button data-bs-dismiss="modal">
                        Fermer
                    </x-button>
                </x-modal.footer>
            </form>
        </x-modal>
    </x-slot:modals>
</x-app-layout>
