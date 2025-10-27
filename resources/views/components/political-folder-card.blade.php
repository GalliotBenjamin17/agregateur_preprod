<x-layouts.card
    group-name="Dossiers politiques"
    name="Tous les dossiers politiques"
    :thin-padding="true"
>
    <x-slot:icon>
        {!! \App\Helpers\IconHelper::politicalFoldersIcon() !!}
    </x-slot:icon>

    <x-slot:actions>
        <x-button type="default" data-bs-toggle="modal" data-bs-target="#add_political_folder">
            Ajouter
        </x-button>
    </x-slot:actions>

    <x-slot:content>
        <div>
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($politicalFolders as $politicalFolder)
                    <a href="{{ route('political-folders.show', ['politicalFolder' => $politicalFolder->slug]) }}" target="_blank" class="py-2 px-6 block hover:bg-gray-50 tippy" data-tippy-content="@datetime($politicalFolder->created_at, capitalized:true) par {{ $politicalFolder->createdBy?->name ?? 'un utilisateur supprimé' }}">
                        <div class="flex space-x-3">
                            <div class="flex-1 space-y-0">
                                <div class="flex flex-col items-start justify-start text-left">
                                    <p class="text-sm text-gray-800 w-full flex items-center ">
                                        <span class="font-semibold">{{ $politicalFolder->name }}</span>
                                        @if($politicalFolder->reference) <span>&nbsp;— {{ $politicalFolder->reference }}</span> @endif
                                        @if($politicalFolder->related_id != $model->id) <span class="ml-auto"> {{ $politicalFolder->related?->name ?? '-' }}</span> @endif
                                    </p>
                                    <p class="text-sm text-gray-800 mt-1">{{ $politicalFolder->content }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <x-empty-model
                        content="Aucun dossier politique référencé"
                        :model="new \App\Models\PoliticalFolder()"
                        class="py-5"
                        height="48"
                    />
                @endforelse
            </ul>
        </div>

        <x-modal id="add_political_folder">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'un dossier politique
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('political-folders.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                    <div class="sm:col-span-2">
                        <x-label value="Nom : " required />
                        <x-input type="text" name="name" placeholder="Ecole élémentaire ..." />
                    </div>

                    <div class="sm:col-span-2">
                        <x-label value="Référence : " />
                        <x-input type="text" name="reference" placeholder="FR-2345" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-label value="Commentaire : " />
                        <x-textarea type="text" placeholder="Co-financement de l'école avec ..." name="content">{{ old('content') }}</x-textarea>
                    </div>

                    <input class="hidden" name="related_type" value="{{ get_class($model) }}">

                    @if($model->children_count > 0)
                        <div class="sm:col-span-2">
                            <x-label value="Ajouter sur une collectivité enfant : " />
                            <x-select type="text" name="related_id">
                                <option value="{{ $model->id }}">Sur cette collectivité ({{ $model->name }})</option>
                                @foreach($model->children as $children)
                                    <option value="{{ $children->id }}">{{ $children->name }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    @else
                        <input class="hidden" name="related_id" value="{{ $model->id }}">
                    @endif
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
    </x-slot:content>
</x-layouts.card>

