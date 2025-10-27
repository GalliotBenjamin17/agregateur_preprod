<x-layouts.card
    :group-name="$inColumn ? 'Fichiers' : null"
    :name="$inColumn ? 'Tous les fichiers' : 'Fichiers (' . $model->files_count . ')'"
>
    <x-slot:icon>
        {!! \App\Helpers\IconHelper::filesIcon(size: $inColumn ? 'lg' : 'sm') !!}
    </x-slot:icon>

    <x-slot:actions>
        <x-button size="sm" data-bs-toggle="modal" data-bs-target="#add_file">
            Ajouter un fichier
        </x-button>
    </x-slot:actions>

    <x-slot:content>
        <x-modals.create-file :model="$model" />
        <div class="p-2.5 sm:p-[1rem] grid grid-cols-1 @if($inColumn) md:grid-cols-2 gap-x-5 gap-y-3 @else md:grid-cols-4 gap-x-16 gap-y-4 @endif">
            @forelse($model->files as $file)
                <x-layouts.card-tile
                    link="{{ route('files.show', ['file' => $file->slug]) }}"
                    :title="$file->name"
                >
                    <x-slot:tiles>
                        <x-layouts.card-tile-line
                                title="Ajout à"
                                :content="$file->created_at->format('H:i d/m/Y')"
                        />
                        <x-layouts.card-tile-line
                                title="Ajouté par"
                                :content="$file->createdBy?->name ?? '-'"
                        />
                    </x-slot:tiles>
                </x-layouts.card-tile>
            @empty
                <x-empty-model
                    content="Aucun fichier référencé"
                    :model="new \App\Models\File()"
                    class="col-span-4"
                    height="48"
                />
            @endforelse
        </div>
    </x-slot:content>
</x-layouts.card>
