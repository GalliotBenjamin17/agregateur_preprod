<x-pages.organizations.details-base
    :organization="$organization"
>
    <x-slot name="cardContent">
        <div class="grid gap-2 md:grid-cols-4">
            <x-card-statistics
                title="Total contributions"
                :number="format($organization->donations_sum_amount ?? 0) . ' € TTC'"
            />

            <x-card-statistics
                title="Tco2 acheté"
                :number="format($sumTco2 ?? 0) . ' tCo2'"
            />
        </div>

        <x-layouts.card
            name="Contributions ({{ $organization->donations_count }})"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::donationsIcon(size: 'sm') !!}
            </x-slot:icon>

            <x-slot:content>
                @if($organization->donations_count > 0)
                    <livewire:tables.donations.index-table :organization="$organization" />
                @else
                    <div class="p-2.5 sm:p-[1rem] grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-4">
                        <x-empty-model
                            content="Aucune contribution rattachée à cette organisation"
                            :model="new \App\Models\Donation()"
                            class="col-span-4"
                            height="48"
                        />
                    </div>
                @endif
            </x-slot:content>
        </x-layouts.card>
    </x-slot>
</x-pages.organizations.details-base>
