<x-app-contributors-2
    :tenant="$tenant"
    :organization="$organization"
>

    <x-contributors-space.section-title
        title="Vos contributions"
        class="py-5 font-semibold"
    />

    <div>
        @if($organization)
            <livewire:interface.tables.donations-table
                :organization="$organization"
            />
        @else
            <livewire:interface.tables.donations-table
                :user="request()->user()"
            />
        @endif
    </div>


    <x-contributors-space.section-title
        title="Statistiques"
        size="md"
        class="mt-10"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ">
                <path fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 0 1 8.25-8.25.75.75 0 0 1 .75.75v6.75H18a.75.75 0 0 1 .75.75 8.25 8.25 0 0 1-16.5 0Z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M12.75 3a.75.75 0 0 1 .75-.75 8.25 8.25 0 0 1 8.25 8.25.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75V3Z" clip-rule="evenodd" />
            </svg>
        </x-slot:icon>
    </x-contributors-space.section-title>

    <div >
        @if($organization)
            <livewire:interface.widgets.cumulative-tons-per-year-bar-chart :organization="$organization" />
        @else
            <livewire:interface.widgets.cumulative-tons-per-year-bar-chart :user="request()->user()" />
        @endif
    </div>


    <x-contributors-space.section-title
        title="Objectifs de développement durable"
        size="md"
        class="mt-10"
    >
        <x-slot:icon>
            <svg viewBox="0 0 24 24" class="h-6 w-6">
                <path fill="currentColor" d="M10.5,3C8,3 6,5 6,7.5C6,8.11 6.13,8.71 6.37,9.27C5.5,10.12 5,11.28 5,12.5C5,15 7,17 9.5,17C10,17 10.5,16.89 11,16.72V21H13V15.77C13.5,15.91 14,16 14.5,16A5.5,5.5 0 0,0 20,10.5A5.5,5.5 0 0,0 14.5,5C14.41,5 14.33,5 14.24,5C13.41,3.76 12,3 10.5,3M10.5,5C11.82,5 12.91,6.03 13,7.35C13.46,7.12 14,7 14.5,7A3.5,3.5 0 0,1 18,10.5A3.5,3.5 0 0,1 14.5,14C13.54,14 12.63,13.61 11.96,12.91C11.76,14.12 10.72,15 9.5,15A2.5,2.5 0 0,1 7,12.5C7,11.12 7.8,10.54 9,9.79C8.2,8.76 8,8.16 8,7.5A2.5,2.5 0 0,1 10.5,5Z"></path>
            </svg>
        </x-slot:icon>
    </x-contributors-space.section-title>

    <x-contributors-space.card>
        <ul class="flex space-x-5 justify-center overflow-x-auto p-5">
            @foreach($sustainableDevelopmentGoals as $sustainableDevelopmentGoal)

                <li>
                    <img src="{{ asset($sustainableDevelopmentGoal->image) }}" class="rounded-md tippy h-24 w-24" data-tippy-content="{{ $sustainableDevelopmentGoal->name }}" alt="Accès à la santé" title="{{  $sustainableDevelopmentGoal->name }}">
                </li>

            @endforeach
        </ul>
        <p class="text-center text-gray-600"> Les objectifs de développement durable (ODD) positivement impactés par les projets auxquels vous avez contribués </p>
    </x-contributors-space.card>

    <x-contributors-space.banner
        background-position="bottom"
        class="!text-white"
        title="Projets de contribution carbone"
        description="Je souhaite contribuer à un projet de séquestration/réduction carbone en Nouvelle-Aquitaine"
        background-image="https://images.pexels.com/photos/1481581/pexels-photo-1481581.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
        button-text="Contribuer"
        button-url="https://larochelle.cooperativecarbone.fr/la-cooperative/les-projets/"
    />



</x-app-contributors-2>
