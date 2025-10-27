<x-layouts.card
    group-name="Commentaires"
    name="Tous les commentaires"
    :thin-padding="true"
>
    <x-slot:icon>
        {!! \App\Helpers\IconHelper::commentsIcon(size: 'lg') !!}
    </x-slot:icon>

    <x-slot:actions>
        <x-button type="default" data-bs-toggle="modal" data-bs-target="#add_comment">
            Ajouter
        </x-button>
    </x-slot:actions>

    <div>
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($comments as $comment)
                <li class="relative py-2 px-6 group flex items-start justify-between">
                    <div class="flex space-x-3 group-hover:blur-sm">
                        <div class="flex-1 space-y-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-800">{{ $comment->content }}</p>
                            </div>
                            <p class="text-[13px] text-gray-500">@datetime($comment->created_at, capitalized:true) par {{ $comment->createdBy?->name ?? 'une personne inconnue' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 hidden group-hover:absolute group-hover:right-5 group-hover:block">
                        <x-button href="#!" data-bs-toggle="modal" data-bs-target="#update_comment_{{ $comment->id }}" type="info">
                            Modifier
                        </x-button>
                        <x-button href="#!" data-bs-toggle="modal" data-bs-target="#delete_comment_{{ $comment->id }}" type="danger">
                            Supprimer
                        </x-button>
                    </div>
                </li>
            @empty
                <x-empty-model
                    content="Aucun commentaire référencé"
                    :model="new \App\Models\Comment()"
                    class="py-5"
                    height="48"
                />
            @endforelse
        </ul>
    </div>

    <x-modal id="add_comment">
        <x-modal.header>
            <div>
                <div class="font-semibold text-gray-700">
                    Ajout d'un commentaire
                </div>
            </div>
            <x-modal.close-button/>
        </x-modal.header>

        <form autocomplete="off" action="{{ route('comments.store') }}" method="POST" enctype='multipart/form-data'>
            @csrf
            <input class="hidden" name="related_id" value="{{ $model->id }}">
            <input class="hidden" name="related_type" value="{{ get_class($model) }}">
            <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                <div class="sm:col-span-2">
                    <x-label value="Commentaire : " required />
                    <x-textarea type="text" required name="content">{{ old('content') }}</x-textarea>
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

    @foreach($comments as $comment)
        <x-modal size="lg" id="update_comment_{{ $comment->id }}">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Modification du commentaire
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('comments.update', ['comment' => $comment->id]) }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                    <div class="sm:col-span-2">
                        <x-label value="Commentaire : " required />
                        <x-textarea type="text" required name="content">{{ $comment->content }}</x-textarea>
                    </div>
                </x-modal.body>

                <x-modal.footer>
                    <x-button data-bs-dismiss="modal">
                        Fermer
                    </x-button>
                    <x-button submit type="success">
                        Mettre à jour
                    </x-button>
                </x-modal.footer>
            </form>
        </x-modal>

        <x-modal id="delete_comment_{{ $comment->id }}">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Confirmation de la suppression du commentaire
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('comments.delete', ['comment' => $comment->id]) }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="border-t border-gray-300">
                    <p>
                        Vous vous apprêtez à supprimer ce commentaire. Cette action est irréversible, vous ne pourrez plus y avoir accès une fois supprimé.
                    </p>
                </x-modal.body>

                <x-modal.footer>
                    <x-button submit type="dangerous">
                        Supprimer le commentaire
                    </x-button>
                    <x-button data-bs-dismiss="modal">
                        Fermer
                    </x-button>
                </x-modal.footer>
            </form>
        </x-modal>
    @endforeach
</x-layouts.card>

