<x-app-contributors-2
    :tenant="$tenant"
    :organization="$organization"
>

    <x-contributors-space.section-title
        title="Ressources"
        class="py-5 font-semibold "
    />

    @if($organization)
        <x-contributors-space.section-title
            title="Vos badges contributeurs"
            class="py-5 !text-sm"
            size="md"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </x-slot:icon>
        </x-contributors-space.section-title>

        <x-contributors-space.card>
            <div class="grid grid-cols-1 md:grid-cols-3">

                @forelse($organization->badges as $badge)
                    <div class="flex flex-col space-y-3">

                        <div class="p-4 text-center flex justify-center tippy" data-tippy-content="{{ $badge->name }}">
                            <img src="{{ asset("/storage/" . $badge->picture) }}" class="h-56">
                        </div>
                        <div class="p-4 flex">
                            <a href="{{ asset("/storage/" . $badge->picture) }}" download target="_blank" class="underline flex items-center hover:text-sky-700 m-auto">
                                 <span class="inline-flex justify-center items-center w-12 h-12 rounded-full text-blue-500 h-5">
                                    <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block">
                                       <path fill="currentColor" d="M15,3H19V0L24,5L19,10V7H15V3M21,11.94V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5A2,2 0 0,1 5,3H12.06C12,3.33 12,3.67 12,4A8,8 0 0,0 20,12C20.33,12 20.67,12 21,11.94M19,18L14.5,12L11,16.5L8.5,13.5L5,18H19Z"></path>
                                    </svg>
                                 </span>
                                    Télécharger l'image
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="col-span-3 text-center">

                        <span class="text-gray-500">Aucun badge sur votre organisation.</span>

                    </div>
                @endforelse

            </div>
        </x-contributors-space.card>

    @endif

    <x-contributors-space.section-title
        title="Communiquer sur votre contribution"
        class="py-5 mt-10 !text-sm"
        size="md"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
            </svg>

        </x-slot:icon>
    </x-contributors-space.section-title>


    <x-contributors-space.card>
        <ul>
            @foreach($communicationDocuments as $document)
                <li>
                    <a href="{{ asset("/storage/" . $document['file']) }}" target="_blank" class="underline flex items-center hover:text-sky-700 mt-1">
                       <span class="inline-flex justify-center items-center w-12 h-12 rounded-full text-blue-500 h-5">
                          <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block">
                             <path fill="currentColor" d="M20 21H4V10H6V19H18V10H20V21M3 3H21V9H3V3M5 5V7H19V5M10.5 11V14H8L12 18L16 14H13.5V11"></path>
                          </svg>
                       </span>
                        {{ $document['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-contributors-space.card>


    <x-contributors-space.section-title
        title="Ressources enjeux énergie-climat"
        class="py-5 mt-10 !text-sm"
        size="md"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
        </x-slot:icon>
    </x-contributors-space.section-title>


    <x-contributors-space.card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            @foreach($externalRessources as $externalRessource)
                <a @if($externalRessource['type'] == 'link') href="{{ $externalRessource['link'] }}" @else href="{{ asset("/storage/" . $externalRessource['file']) }}" @endif target="_blank" class="underline flex items-center hover:text-sky-700 mt-1">
                   <span class="inline-flex justify-center items-center w-12 h-12 rounded-full text-blue-500 h-5">
                       @if($externalRessource['type'] == 'link')
                           <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block">
                             <path fill="currentColor" d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"></path>
                          </svg>
                       @else
                           <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block">
                             <path fill="currentColor" d="M20 21H4V10H6V19H18V10H20V21M3 3H21V9H3V3M5 5V7H19V5M10.5 11V14H8L12 18L16 14H13.5V11"></path>
                          </svg>
                       @endif
                   </span>

                    {{ $externalRessource['title'] }}

                    @if($externalRessource['type'] == 'link')
                        (Lien externe)
                    @endif
                </a>
            @endforeach

        </div>
    </x-contributors-space.card>



</x-app-contributors-2>
