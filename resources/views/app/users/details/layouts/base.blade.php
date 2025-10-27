
<x-app-layout>
    <x-slot:content>
        <x-layouts.card
            group-name="Acteurs"
            :name="$user->name"
            :thin-padding="false"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::usersIcon() !!}
            </x-slot:icon>

            <x-slot:content>
                @section('title', $user->name)
            </x-slot:content>
        </x-layouts.card>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3">
            <div class="{{ Route::is('users.show.details') ? 'md:col-span-8' : 'md:col-span-12' }}">
                <div class="bg-white rounded-md border border-gray-300 p-2.5 sm:p-[1rem]">
                    <x-menu-card-group>
                        <x-menu-tab content="Détails" :url="route('users.show.details', ['user' => $user->slug])" :is-active="request()->routeIs('users.show.details')" />
                        <x-menu-tab content="Contributions" :url="route('users.show.donations', ['user' => $user->slug])" :is-active="request()->routeIs('users.show.donations')" />
                        <x-menu-tab content="Fichiers" :url="route('users.show', ['user' => $user->slug])" :is-active="request()->routeIs('users.show')" />
                    </x-menu-card-group>
                    <div class="mt-5 space-y-3">
                        @isset($cardContent)
                            {{ $cardContent }}
                        @endisset
                    </div>
                </div>
            </div>

            @if (Route::is('users.show.details'))
            <div class="md:col-span-4">
                @isset($colContent)
                    {{ $colContent }}
                @endisset
            </div>
            @endif

        </div>
    </x-slot:content>

    <x-slot:modals>
        @isset($modals)
            {{ $modals }}
        @endisset

            <x-modal id="delete_user">
                <x-modal.header class="bg-red-500">
                    <div>
                        <div class="font-semibold text-white">
                            Confirmation de la suppression de l'utilisateur
                        </div>
                    </div>
                    <x-modal.close-button/>
                </x-modal.header>

                <form autocomplete="off" action="{{ route('users.delete', ['user' => $user]) }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                    @method('DELETE')
                    <x-modal.body class="border-t border-gray-300">
                        <p>
                            Vous vous apprêtez à supprimer cet utilisateur. Cette action est irréversible !
                        </p>
                    </x-modal.body>

                    <x-modal.footer>
                        <x-button submit type="dangerous">
                            Supprimer l'utilisateur
                        </x-button>
                        <x-button data-bs-dismiss="modal">
                            Fermer
                        </x-button>
                    </x-modal.footer>
                </form>
            </x-modal>
    </x-slot:modals>
</x-app-layout>
