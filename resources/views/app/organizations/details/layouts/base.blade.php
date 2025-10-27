<x-app-layout>
    <x-slot:content>
        <x-layouts.card
            group-name="Organisations"
            :name="$organization->name"
            :thin-padding="false"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::organizationsIcon(size: 'lg') !!}
            </x-slot:icon>

        </x-layouts.card>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3">
            <div class="{{ Route::is('organizations.show.details') ? 'md:col-span-8' : 'md:col-span-12' }}">
                <div class="bg-white rounded-md border border-gray-300 p-2.5 sm:p-[1rem]">
                    <x-menu-card-group>
                        <x-menu-tab content="Détails" :url="route('organizations.show.details', ['organization' => $organization->slug])" :is-active="request()->routeIs('organizations.show.details')" />
                        <x-menu-tab content="Représentants" :url="route('organizations.show.users', ['organization' => $organization->slug])" :is-active="request()->routeIs('organizations.show.users')" />
                        <x-menu-tab content="Contributions" :url="route('organizations.show.donations', ['organization' => $organization->slug])" :is-active="request()->routeIs('organizations.show.donations')" />
                        <x-menu-tab content="Projets liés" :url="route('organizations.show.projects', ['organization' => $organization->slug])" :is-active="request()->routeIs('organizations.show.projects')" />
                        <x-menu-tab content="Fichiers" :url="route('organizations.show', ['organization' => $organization->slug])" :is-active="request()->routeIs('organizations.show')" />

                    </x-menu-card-group>
                    <div class="mt-5 space-y-3">
                        @isset($cardContent)
                            {{ $cardContent }}
                        @endisset
                    </div>
                </div>
            </div>

            <div class="md:col-span-4 space-y-2 sm:space-y-3">
                @isset($colContent)
                    {{ $colContent }}
                @endisset

                @if (Route::is('organizations.show.details'))
                    <x-comments-card :model="$organization" />
                    <x-activities-model :model="$organization" />
                @endif

            </div>
        </div>
    </x-slot:content>

    <x-slot:modals>
        @isset($modals)
            {{ $modals }}
        @endisset
    </x-slot:modals>
</x-app-layout>
