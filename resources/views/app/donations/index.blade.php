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
                        <x-button type="default" data-bs-toggle="modal" data-bs-target="#add_donation">
                            Réclamer un paiement
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

    <x-slot:modals>
        <x-modal id="add_donation" size="lg">
            <x-modal.header>
                <div>
                    <div class="font-semibold text-gray-700">
                        Ajout d'une nouvelle contribution
                    </div>
                </div>
                <x-modal.close-button/>
            </x-modal.header>

            <x-modal.body>
                <livewire:forms.create-donation-form />
            </x-modal.body>
        </x-modal>
    </x-slot:modals>
</x-app-layout>
