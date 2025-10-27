<x-app-contributors-2
    :tenant="$tenant"
    :organization="$organization"
>

    <div class="w-full">
        <div class="block sm:flex items-center justify-between">
            <x-contributors-space.section-title
                :title="'Projet : ' . $project->name "
                class="py-5 font-semibold "
            />

            <div>
                <livewire:interface.actions.donate-action
                    :project="$project"
                />
            </div>
        </div>

        @if(!$project->hasParent() and get_class($project->state) != \App\States\Project\Abandoned::class)

            <x-contributors-space.section-title
                title="Statut du projet"
                class="pt-5"
                size="md"
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>

                </x-slot:icon>
            </x-contributors-space.section-title>

            @if($project->certification_state)
                <div class="md:col-span-12 space-y-2">
                    <x-layouts.steps :steps-count="5">
                        <x-slot name="steps">
                            @foreach([
                                new \App\States\Certification\Notified($project),
                                new \App\States\Certification\Evaluated($project),
                                new \App\States\Certification\Certified($project),
                                new \App\States\Certification\Verified($project),
                                new \App\States\Certification\Approved($project),
                            ] as $state)
                                <x-layouts.step
                                    :title="$state->humanName()"
                                    :done="$project->certification_state->rank() > $state->rank()"
                                    :pending="$project->certification_state->rank() == $state->rank()"
                                >
                                    <x-slot name="icon">
                                        {!! $state->icon() !!}
                                    </x-slot>
                                </x-layouts.step>
                            @endforeach
                        </x-slot>
                    </x-layouts.steps>
                </div>
            @endif
        @endif

        <x-contributors-space.section-title
            title="Statistiques"
            class="py-5 mt-10"
            size="md"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
            </x-slot:icon>
        </x-contributors-space.section-title>


        @if($organization)
            <livewire:interface.widgets.spread-tons-per-year-line-chart :organization="$organization" :project="$project" />
        @else
            <livewire:interface.widgets.spread-tons-per-year-line-chart :user="request()->user()"  :project="$project"/>
        @endif


        <x-contributors-space.section-title
            title="Ressources liées au projet"
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
            <div class="grid grid-cols-2 gap-4">

                @if(is_array($project->contributors_files) and sizeof($project->contributors_files) > 0)
                    @foreach($project->contributors_files as $resource)
                        @php
                            if (!\Illuminate\Support\Arr::has($resource, 'type')) {
                                if (\Illuminate\Support\Arr::has($resource, 'file') and \Illuminate\Support\Arr::get($resource, 'file')) {
                                    $resource['type'] = "file";
                                }

                                if (\Illuminate\Support\Arr::has($resource, 'link') and \Illuminate\Support\Arr::get($resource, 'link')) {
                                    $resource['type'] = "link";
                                }
                            }
                        @endphp
                        @if($resource['type']  === 'file')
                            <a href="{{ asset("/storage/" . $resource['file']) }}" target="_blank" class="underline flex items-center hover:text-sky-700 mt-1">
                               <span class="inline-flex justify-center items-center w-12  rounded-full text-blue-500 h-5">
                                  <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block">
                                     <path fill="currentColor" d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3M9.5 11.5C9.5 12.3 8.8 13 8 13H7V15H5.5V9H8C8.8 9 9.5 9.7 9.5 10.5V11.5M14.5 13.5C14.5 14.3 13.8 15 13 15H10.5V9H13C13.8 9 14.5 9.7 14.5 10.5V13.5M18.5 10.5H17V11.5H18.5V13H17V15H15.5V9H18.5V10.5M12 10.5H13V13.5H12V10.5M7 10.5H8V11.5H7V10.5Z"></path>
                                  </svg>
                               </span>
                                {{ $resource['title'] }}
                            </a>
                        @endif

                        @if($resource['type'] === 'link')
                            <a href="{{ $resource['link'] }}" target="_blank" class="underline flex items-center hover:text-sky-700 mt-1">
                               <span class="inline-flex justify-center items-center w-12  rounded-full text-blue-500 h-5">
                                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                    </svg>
                               </span>
                                {{ $resource['title'] }}
                            </a>
                        @endif


                    @endforeach
                @else
                    <div class="col-span-3 text-center">

                        <span class="text-gray-500">Aucun fichier sur ce projet.</span>

                    </div>
                @endif


            </div>
        </x-contributors-space.card>

        <x-contributors-space.banner
            background-position="top"
            title="Projets de contribution carbone"
            description="Je souhaite contribuer à un projet de séquestration/réduction carbone en Nouvelle-Aquitaine"
            background-image="https://images.pexels.com/photos/1481581/pexels-photo-1481581.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
            button-text="Voir tous les projets"
            button-url="https://larochelle.cooperativecarbone.fr/la-cooperative/les-projets/"
        />
    </div>

</x-app-contributors-2>
