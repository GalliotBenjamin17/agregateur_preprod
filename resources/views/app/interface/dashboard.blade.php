<x-app-contributors-2
    :tenant="$tenant"
    :organization="$organization"
>

    @if($tenant->contributor_space_banner_activated)
        <x-contributors-space.banner
            :title="$tenant->contributor_space_banner_title"
            :description="$tenant->contributor_space_banner_description"
            :background-image="$tenant->contributor_space_banner_picture"
            :button-text="$tenant->contributor_space_banner_button_text"
            :button-url="$tenant->contributor_space_banner_button_url"
        />
    @endif

    <x-contributors-space.section-title
        title="Vos contributions carbone en bref"
        size="md"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ">
                <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z"/>
            </svg>
        </x-slot:icon>
    </x-contributors-space.section-title>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">

        <x-contributors-space.statistics-card
            title="Contributions"
            :number="$donationsCount"
        >
            <x-slot:icon>
                <svg viewBox="0 0 24 24" width="48" height="48" class="inline-block h-16 text-emerald-500">
                    <path fill="currentColor" d="M3,14L3.5,14.07L8.07,9.5C7.89,8.85 8.06,8.11 8.59,7.59C9.37,6.8 10.63,6.8 11.41,7.59C11.94,8.11 12.11,8.85 11.93,9.5L14.5,12.07L15,12C15.18,12 15.35,12 15.5,12.07L19.07,8.5C19,8.35 19,8.18 19,8A2,2 0 0,1 21,6A2,2 0 0,1 23,8A2,2 0 0,1 21,10C20.82,10 20.65,10 20.5,9.93L16.93,13.5C17,13.65 17,13.82 17,14A2,2 0 0,1 15,16A2,2 0 0,1 13,14L13.07,13.5L10.5,10.93C10.18,11 9.82,11 9.5,10.93L4.93,15.5L5,16A2,2 0 0,1 3,18A2,2 0 0,1 1,16A2,2 0 0,1 3,14Z"></path>
                </svg>
            </x-slot:icon>
        </x-contributors-space.statistics-card>

        <x-contributors-space.statistics-card
            title="Nombre de projets soutenus"
            :number="$projectsCount"
        >
            <x-slot:icon>
                <svg viewBox="0 0 24 24" width="48" height="48" class="inline-block h-16 text-emerald-500">
                    <path fill="currentColor" d="M16 12L9 2L2 12H3.86L0 18H7V22H11V18H18L14.14 12H16M20.14 12H22L15 2L12.61 5.41L17.92 13H15.97L19.19 18H24L20.14 12M13 19H17V22H13V19Z"></path>
                </svg>
            </x-slot:icon>
        </x-contributors-space.statistics-card>

        <x-contributors-space.statistics-card
            title="Tonnage Co2 financés"
            :number="format($tonsCount, 2) . 'T'"
        >
            <x-slot:icon>
                <svg viewBox="0 0 24 24" width="48" height="48" class="inline-block h-16 text-emerald-500">
                    <path fill="currentColor" d="M11,2V22C5.9,21.5 2,17.2 2,12C2,6.8 5.9,2.5 11,2M13,2V11H22C21.5,6.2 17.8,2.5 13,2M13,13V22C17.7,21.5 21.5,17.8 22,13H13Z"></path>
                </svg>
            </x-slot:icon>
        </x-contributors-space.statistics-card>

    </div>

    <x-contributors-space.section-title
        title="Statistiques"
        size="md"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ">
                <path fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 0 1 8.25-8.25.75.75 0 0 1 .75.75v6.75H18a.75.75 0 0 1 .75.75 8.25 8.25 0 0 1-16.5 0Z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M12.75 3a.75.75 0 0 1 .75-.75 8.25 8.25 0 0 1 8.25 8.25.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75V3Z" clip-rule="evenodd" />
            </svg>
        </x-slot:icon>
    </x-contributors-space.section-title>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            @if($donationsCount > 0)
                <div class="col-span-2">
                    @if($organization)
                        <livewire:interface.widgets.yearly-ton-sum :organization="$organization" />
                    @else
                        <livewire:interface.widgets.yearly-ton-sum :user="request()->user()" />
                    @endif
                </div>
            @endif

            <div>
                @if($organization)
                    <livewire:interface.widgets.tons-sum-doughnut :organization="$organization" />
                @else
                    <livewire:interface.widgets.tons-sum-doughnut :user="request()->user()" />
                @endif
            </div>

            <div >
                @if($organization)
                    <livewire:interface.widgets.cumulative-tons-per-year-bar-chart :organization="$organization" />
                @else
                    <livewire:interface.widgets.cumulative-tons-per-year-bar-chart :user="request()->user()" />
                @endif
            </div>

        </div>

    <x-contributors-space.banner
        background-position="top"
        title="Projets de contribution carbone"
        description="Je souhaite contribuer à un projet de séquestration/réduction carbone en Nouvelle-Aquitaine"
        background-image="https://images.pexels.com/photos/158827/field-corn-air-frisch-158827.jpeg"
        button-text="Voir tous les projets"
        button-url="https://larochelle.cooperativecarbone.fr/la-cooperative/les-projets/"
    />


    <x-contributors-space.section-title
        title="Contributions"
        size="md"
    >
        <x-slot:icon>
            <svg viewBox="0 0 24 24" width="20" height="20" class="w-6 h-6">
                <path fill="currentColor" d="M10.5,3C8,3 6,5 6,7.5C6,8.11 6.13,8.71 6.37,9.27C5.5,10.12 5,11.28 5,12.5C5,15 7,17 9.5,17C10,17 10.5,16.89 11,16.72V21H13V15.77C13.5,15.91 14,16 14.5,16A5.5,5.5 0 0,0 20,10.5A5.5,5.5 0 0,0 14.5,5C14.41,5 14.33,5 14.24,5C13.41,3.76 12,3 10.5,3M10.5,5C11.82,5 12.91,6.03 13,7.35C13.46,7.12 14,7 14.5,7A3.5,3.5 0 0,1 18,10.5A3.5,3.5 0 0,1 14.5,14C13.54,14 12.63,13.61 11.96,12.91C11.76,14.12 10.72,15 9.5,15A2.5,2.5 0 0,1 7,12.5C7,11.12 7.8,10.54 9,9.79C8.2,8.76 8,8.16 8,7.5A2.5,2.5 0 0,1 10.5,5Z"></path>
            </svg>
        </x-slot:icon>
    </x-contributors-space.section-title>

    @if($organization)
            <livewire:interface.tables.donations-table
                :organization="$organization"
            />
    @else
            <livewire:interface.tables.donations-table
                :user="request()->user()"
            />
    @endif

    <x-contributors-space.banner
        background-position="bottom"
        class="!text-white"
        title="Vous pouvez également nous laisser choisir un ou des projets"
        description="Laissez nous choisir un ou des projets pour vous !"
        background-image="https://images.pexels.com/photos/1481581/pexels-photo-1481581.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
        button-text="Contribuer"
        button-url="https://larochelle.cooperativecarbone.fr/contribution/"
    />


</x-app-contributors-2>
