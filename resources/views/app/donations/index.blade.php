<x-app-layout>
    <x-slot name="content">
        @section('title', "Contributions")
        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Contributions"
                    name="Toutes les contributions"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::donationsIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        <x-button type="default" class="inline-flex" href="{{ route('transactions.index') }}" icon>
                            <span>Transactions</span>
                            <x-icon.lien_externe class="h-4 w-4 text-gray-500" />
                        </x-button>
                        @role('admin|local_admin')
                            <livewire:actions.donations.create-form />
                        @endrole
                    </x-slot:actions>

                    <x-slot:content>
                        <livewire:tables.donations.index-table />
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>

</x-app-layout>
