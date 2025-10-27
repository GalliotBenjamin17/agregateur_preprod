<x-app-layout>
    <x-slot:content>
        <x-layouts.card
            group-name="Partenaires"
            :name="$partner->name"
            :thin-padding="false"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::partnersIcon(size: 'lg') !!}
            </x-slot:icon>
        </x-layouts.card>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3">
            <div class="{{ Route::is('partners.show') ? 'md:col-span-8' : 'md:col-span-12' }}">
                <div class="bg-white rounded-md border border-gray-300 p-2.5 sm:p-[1rem]">
                    <x-menu-card-group>
                        <x-menu-tab content="Détails" :url="route('partners.show', ['partner' => $partner])" :is-active="request()->routeIs('partners.show')" />
                        <x-menu-tab content="Projets" :url="route('partners.show.projects', ['partner' => $partner])" :is-active="request()->routeIs('partners.show.projects')" />
                        <x-menu-tab content="Utilisateurs" :url="route('partners.show.users', ['partner' => $partner])" :is-active="request()->routeIs('partners.show.users')" />
                        <x-menu-tab content="Statistiques" :url="route('partners.show.statistics', ['partner' => $partner])" :is-active="request()->routeIs('partners.show.statistics')" />
                        <x-menu-tab content="Fichiers" :url="route('partners.show.relationships', ['partner' => $partner])" :is-active="request()->routeIs('partners.show.relationships')" />
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

                @if (Route::is('partners.show'))
                    <x-comments-card :model="$partner" />
                    <x-activities-model :model="$partner" />
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
