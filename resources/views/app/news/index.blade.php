<x-app-layout>
    <x-slot name="content">
        @section('title', "Actualités")

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Actualités"
                    name="Toutes les actualités"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::newsIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        @role('admin|local_admin')
                           <livewire:actions.news.sync-news />
                           <livewire:actions.news.create-form />
                        @endrole
                    </x-slot:actions>

                    <x-slot:content>
                        <livewire:tables.news.index-table />
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>
</x-app-layout>
