<x-pages.donations.details-base
    :donation="$donation"
>
    <x-slot name="cardContent">
        <div class="pt-3 pb-5 px-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
            <div class="flex justify-between mb-1">
                <span class="text-base font-medium dark:text-white">Répartition</span>
                <span class="text-sm font-medium dark:text-white">{{ format($amountSplit) }} € / {{ format($donation->amount) }} €</span>
            </div>
            <div class="w-full bg-gray-300 rounded-full overflow-hidden h-4 dark:bg-gray-700">
                <div class="bg-green-600 text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="width: {{ round(($amountSplit / $donation->amount) * 100) }}%"> {{ round(($amountSplit / $donation->amount) * 100) }}%</div>
            </div>
        </div>


        <x-layouts.card
            name="Fléchages effectués - {{ format($amountSplit) }} €"
            :thin-padding="true"
        >
            <x-slot:actions>
                @if(!$donation->is_donation_splits_full)
                    <x-button href="#!" data-bs-toggle="modal" data-bs-target="#add_split">
                        Flécher sur des projets
                    </x-button>
                @endif
            </x-slot:actions>

            <x-slot:content>
                <livewire:tables.donations.donation-splits-table :donation="$donation" />
            </x-slot:content>
        </x-layouts.card>
    </x-slot>

    <x-slot name="colContent">
        <div class="col-span-4 space-y-2 sm:space-y-3">
            <x-comments-card :model="$donation" />
            <x-activities-model :model="$donation" />
        </div>
    </x-slot>

    <x-slot:modals>
        @if(!$donation->is_donation_splits_full)
            <x-modal id="add_split" size="lg">
                <x-modal.header>
                    <div>
                        <div class="font-semibold text-gray-700">
                            Ajout d'une nouvelle répartition
                        </div>
                    </div>
                    <x-modal.close-button/>
                </x-modal.header>

                <x-modal.body>
                    <livewire:forms.donations.donation-split-form :donation="$donation" />
                </x-modal.body>
            </x-modal>
        @endif
    </x-slot:modals>
</x-pages.donations.details-base>
