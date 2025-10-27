<x-app-layout>
    <x-slot:content>
        <x-layouts.card
            group-name="Instances locales"
            :name="$tenant->name"
            :thin-padding="false"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::tenantIcon(size: 'lg') !!}
            </x-slot:icon>

            <x-slot:actions>
                <x-reminder-widget :model="$tenant" />
            </x-slot:actions>

            <x-slot:content>
                <x-layouts.card-content-attributes>
                    <x-layouts.card-content-attribute
                        label="Création"
                        :value="\Carbon\Carbon::userDatetime($tenant->created_at, capitalized: true)"
                    />
                </x-layouts.card-content-attributes>
            </x-slot:content>
        </x-layouts.card>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3">
            <div class="md:col-span-8">
                <div class="bg-white rounded-md border border-gray-300 p-2.5 sm:p-[1rem]">
                    <x-menu-card-group>
                        <x-menu-tab content="Associés" :url="route('tenants.show', ['tenant' => $tenant->slug])" :is-active="request()->routeIs('tenants.show')" />
                        <x-menu-tab content="Relations" :url="route('tenants.show.details', ['tenant' => $tenant->slug])" :is-active="request()->routeIs('tenants.show.details')" />
                    </x-menu-card-group>
                    <div class="mt-5 space-y-3">
                        @isset($cardContent)
                            {{ $cardContent }}
                        @endisset
                    </div>
                </div>
            </div>

            <div class="md:col-span-4">
                @isset($colContent)
                    {{ $colContent }}
                @endisset
            </div>
        </div>
    </x-slot:content>

    <x-slot:modals>
        @isset($modals)
            {{ $modals }}
        @endisset
    </x-slot:modals>
</x-app-layout>
