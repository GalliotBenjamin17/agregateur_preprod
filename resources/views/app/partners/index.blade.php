<x-app-layout>
    <x-slot name="content">
        @section('title', "Partenaires")

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Partenaires"
                    name="Tous les partenaires"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::partnersIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        @role('admin|local_admin')
                            <livewire:actions.partners.create-form />
                        @endrole
                    </x-slot:actions>

                    <x-slot:content>
                        <livewire:tables.partners.index-table />
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>
</x-app-layout>
