<x-app-layout>
    <x-slot name="content">

        @section('title', "Compatibilité")

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Comptabilité"
                    name="Tous les exports"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::donationsIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>

                        <livewire:actions.financial-exports.create-form />

                    </x-slot:actions>

                    <x-slot:content>
                        <livewire:tables.financial-exports.index-table />
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>
</x-app-layout>
