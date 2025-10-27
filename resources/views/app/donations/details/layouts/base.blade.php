<x-app-layout>
    <x-slot:content>
        <x-layouts.card
            group-name="Contributions"
            :name="$getDisplayedName()"
            :thin-padding="false"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::donationsIcon(size: 'lg') !!}
            </x-slot:icon>

            <x-slot:actions>
                <div class="flex items-center space-x-1">
                    <form method="POST" action="{{ route('donations.download.certificate', ['donation' => $donation]) }}">
                        @csrf
                        <x-button icon submit>
                            <x-icon.telecharger class="h-5 w-5" />
                            <span>Certificat</span>
                        </x-button>
                    </form>

                    {{-- <x-reminder-widget :model="$donation" /> --}}
                </div>
            </x-slot:actions>

            <x-slot:content>
                @section('title', $getDisplayedName())

                <x-layouts.card-content-attributes>
                    <x-layouts.card-content-attribute
                        label="Donateur"
                    >
                        <x-slot:value>
                            <a class="link" href="{{ $donation->related ? (method_exists($donation->related, 'redirectRouter') ? $donation->related->redirectRouter() : '#!') : '#!' }}">
                                <span>{{ $donation->related?->name ?? 'Inconnu' }}</span>
                            </a>
                        </x-slot:value>
                    </x-layouts.card-content-attribute>
                    <x-layouts.card-content-attribute
                        label="Source"
                        :value="Arr::get(config('values.donations.map'), $donation->source)"
                    />
                    <x-layouts.card-content-attribute
                        label="Montant"
                        :value="format($donation->amount) . ' €'"
                    />
                    <x-layouts.card-content-attribute
                        label="Création"
                        :value="\Carbon\Carbon::userDatetime($donation->created_at, capitalized: true)"
                    />
                </x-layouts.card-content-attributes>
            </x-slot:content>
        </x-layouts.card>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3">
            <div class="{{ Route::is('donations.show.details') ? 'md:col-span-8' : 'md:col-span-12' }}">
                <div class="bg-white rounded-md border border-gray-300 p-2.5 sm:p-[1rem]">
                    <x-menu-card-group>
                        <x-menu-tab content="Détails" :url="route('donations.show.details', ['donation' => $donation->id])" :is-active="request()->routeIs('donations.show.details')" />
                        <x-menu-tab content="Fléchage" :url="route('donations.show.split', ['donation' => $donation->id])" :is-active="request()->routeIs('donations.show.split')" />
                        {{-- <x-menu-tab content="Relations" :url="route('donations.show', ['donation' => $donation->id])" :is-active="request()->routeIs('donations.show')" /> --}}
                    </x-menu-card-group>
                    <div class="mt-5 space-y-3">
                        @isset($cardContent)
                            {{ $cardContent }}
                        @endisset
                    </div>
                </div>
            </div>

            @if (Route::is('donations.show.details'))
            <div class="md:col-span-4">
                @isset($colContent)
                    {{ $colContent }}
                @endisset
            </div>
            @endif

        </div>
    </x-slot:content>

    <x-slot:modals>
        @isset($modals)
            {{ $modals }}
        @endisset
    </x-slot:modals>
</x-app-layout>
