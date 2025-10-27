<x-app-layout>
    <x-slot name="content">
        @section('title', "Organisations")

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Organisations"
                    name="Toutes les organisations"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::organizationsIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        @role('admin|local_admin')
                            <x-button data-bs-toggle="modal" data-bs-target="#add_organization">
                                Ajouter une organisation
                            </x-button>
                        @endrole
                    </x-slot:actions>

                    <x-slot:content>
                        <livewire:tables.organizations.index-table />
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>

    <x-slot:modals>
        @role('admin|local_admin')
            <x-modal id="add_organization">
                <x-modal.header>
                    <div>
                        <div class="font-semibold text-gray-700">
                            Ajout d'une organisation
                        </div>
                    </div>
                    <x-modal.close-button/>
                </x-modal.header>

                <form autocomplete="off" action="{{ route('organizations.store') }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                    <x-modal.body class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-300">
                        <div class="sm:col-span-2">
                            <x-label value="Nom : " required />
                            <x-input type="text" name="name" :value="old('name')" placeholder="Entreprise ..." required />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label value="Siret : " required />
                            <x-input type="text" name="siret" :value="old('siret')" placeholder="8783027510..." required />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label value="Type d'organisation : " required />
                            <x-select name="organization_type_id">
                                @foreach($organizationTypes as $organizationType)
                                    <option @selected(old('organization_type_id') == $organizationType->id) value="{{ $organizationType->id }}">{{ $organizationType->name }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <x-tenant-form name="tenant_id" />
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
