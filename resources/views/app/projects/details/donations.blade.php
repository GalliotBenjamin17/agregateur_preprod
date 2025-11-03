<x-pages.projects.details-base :project="$project">
    <x-slot name="fullContent">
        @if ($donationsAffiliated > 0)
            <div class="pt-3 pb-5 px-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                <div class="flex justify-between mb-1">
                    <span class="text-base font-medium">Financement</span>
                    <div class="text-right">
                        <div class="text-sm font-medium">{{ format($donationsAffiliated) }} € TTC /
                            {{ format($project->cost_global_ttc ?? 0) }} € TTC</div>
                        <div class="text-xs text-gray-600">
                            {{ format(\App\Helpers\TVAHelper::getHT($donationsAffiliated)) }} € HT /
                            {{ format(\App\Helpers\TVAHelper::getHT($project->cost_global_ttc ?? 0)) }} € HT</div>
                    </div>
                </div>
                <div class="w-full bg-gray-300 rounded-full h-4 overflow-hidden dark:bg-gray-700">
                    @if ($project->cost_global_ttc > 0)
                        <div class="bg-green-600 text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                            style="width: {{ $donationsAffiliated < $project->cost_global_ttc ? round(($donationsAffiliated / $project->cost_global_ttc) * 100) : '100' }}%">
                            {{ format(round(($donationsAffiliated / $project->cost_global_ttc) * 100)) }}%</div>
                    @else
                        <div class="bg-green-600 text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                            style="width: 100%">100%</div>
                    @endif
                </div>
                @php
                    $costTtc = $project->cost_global_ttc ?? 0;
                    $remainingTtc = max($costTtc - $donationsAffiliated, 0);
                    $remainingHt = \App\Helpers\TVAHelper::getHT($remainingTtc);
                @endphp
                <div class="mt-2 text-xs text-gray-700">
                    Reste à financer: <span class="font-medium">{{ format($remainingTtc) }} € TTC</span>
                    ({{ format($remainingHt) }} € HT)
                </div>
            </div>
        @endif

        @if ($tonnesAffiliated > 0)
            <div class="pt-3 pb-5 px-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                <div class="flex justify-between mb-1">
                    <span class="text-base font-medium">Tonnes CO2 vendues (base HT)</span>
                    <span class="text-sm font-medium">{{ format($tonnesAffiliated) }} tCO2e /
                        {{ format($project->tco2 ?? 0) }} tCO2e</span>
                </div>
                @php
                    $tco2Total = $project->tco2 ?? 0;
                    $tco2Remaining = max($tco2Total - $tonnesAffiliated, 0);
                @endphp
                <div class="w-full bg-gray-300 rounded-full h-4 overflow-hidden dark:bg-gray-700">
                    @if ($project->tco2 > 0)
                        <div class="bg-blue-600 text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                            style="width: {{ $tonnesAffiliated < $project->tco2 ? round(($tonnesAffiliated / $project->tco2) * 100) : '100' }}%">
                            {{ format(round(($tonnesAffiliated / $project->tco2) * 100)) }}%</div>
                    @else
                        <div class="bg-blue-600 text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                            style="width: 100%">100%</div>
                    @endif
                </div>
                <div class="mt-2 text-xs text-gray-700">
                    Reste à financer: <span class="font-medium">{{ format($tco2Remaining) }} tCO2e</span>
                </div>
            </div>
        @endif

        <x-layouts.card name="Contributions" :thin-padding="true">
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::donationsIcon(size: 'sm') !!}
            </x-slot:icon>

            <x-slot:content>
                @if ($project->donation_splits_count > 0)
                    <style>
                        .filament-tables-table-container {
                            border-top-left-radius: 0px !important;
                            border-top-right-radius: 0px !important;
                        }
                    </style>

                    <livewire:tables.donations.donation-splits-project-table :project="$project" />
                @else
                    <div class="p-2.5 sm:p-[1rem] flex items-center flex-wrap gap-x-5 gap-y-2">
                        <x-empty-model content="Aucune contribution flǸchǸe" :model="new \App\Models\Donation()" class="col-span-4"
                            height="48" />
                    </div>
                @endif
            </x-slot:content>
        </x-layouts.card>
    </x-slot>
</x-pages.projects.details-base>