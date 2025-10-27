<x-app-layout>
    <x-slot name="content">
        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Instances locales"
                    name="Toutes les antennes locales"
                    :without-header-padding-bottom="true"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::tenantIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        <x-button data-bs-toggle="modal" data-bs-target="#add_tenant">
                            Ajouter une instance
                        </x-button>
                    </x-slot:actions>

                    <x-slot:content>
                        <livewire:tables.settings.tenants-table />
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modal id="add_tenant">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'une instance
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <form autocomplete="off" action="{{ route('tenants.store') }}" method="POST" enctype='multipart/form-data'>
                @csrf
                <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                    <div class="sm:col-span-2">
                        <x-label value="Nom : " required />
                        <x-input type="text" name="name" placeholder="Nom de l'instance ..." />
                    </div>

                    <div class="sm:col-span-2">
                        <x-label value="Sous-domaine : " required />
                        <x-input type="text" name="domain" placeholder="XXX.cooperativecarbone.fr" />
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
    </x-slot:modals>
</x-app-layout>
