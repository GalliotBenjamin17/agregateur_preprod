<x-app-layout>
    @section('title', "Projets")

    <x-slot name="content">
        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Projets"
                    name="Tous les projets"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::projectIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        @role('admin|local_admin')
                            <livewire:actions.projects.sync-projects />

                            <x-button data-bs-toggle="modal" data-bs-target="#add_project">
                                Ajouter un projet
                            </x-button>
                        @endrole
                    </x-slot:actions>

                    <livewire:tables.projects.index-table />
                </x-layouts.card>
            </div>
        </div>
    </x-slot>

    <x-slot:modals>
        @role('admin|local_admin')
            <x-modal id="add_project">
                <x-modal.header>
                    <div>
                        <div class="font-semibold text-gray-700">
                            Ajout d'un projet
                        </div>
                    </div>
                    <x-modal.close-button/>
                </x-modal.header>

                <form autocomplete="off" action="{{ route('projects.store') }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                    <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                        <div class="sm:col-span-2">
                            <x-label value="Nom : " required />
                            <x-input type="text" name="name" placeholder="Nom du projet ..." />
                        </div>

                        <div class="sm:col-span-2">
                            <x-tenant-form name="tenant_id" />

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
        @endrole
    </x-slot:modals>
</x-app-layout>
