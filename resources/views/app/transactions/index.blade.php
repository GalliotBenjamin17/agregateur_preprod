<x-app-layout>
    <x-slot name="content">
        @section('title', "Transactions")

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Contributions"
                    name="Toutes les transactions"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::transactionsIcon(size: 'lg') !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        <x-button href="{{ route('donations.index') }}">
                            Contributions
                        </x-button>
                    </x-slot:actions>

                    <x-slot:content>
                        <livewire:tables.transactions.index-table />
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>
</x-app-layout>
