<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">
        <div class="mb-6">
            <livewire:forms.projects.certification-tenant-form :project="$project" />
        </div>
        @if($project->hasParent())
            <x-files-component :model="$project" />
        @elseif($project->hasMethodForm())
            <div class="flex items-center justify-between mb-5">
                <div class="font-bold">
                    Méthode sélectionnée : {{ $project->methodForm->name }}
                </div>

                <div>
                    @if(get_class($project->certification_state) != \App\States\Certification\Approved::class)
                        <form method="POST" action="{{ route('projects.update.next-certification-state', ['project' => $project]) }}">
                            <x-button submit type="info" icon>
                                <span>Prochaine étape</span>
                                <x-icon.chevron_droite class="h-5 w-5" />
                            </x-button>
                            @csrf
                        </form>
                    @endif
                </div>
            </div>

            <div class="p-2 bg-gray-100 rounded-md">
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

            <div class="pt-5">
                <livewire:forms.projects.method-form-replies-form
                    :method-form="$project->methodForm"
                    :project="$project"
                />
            </div>
        @else
            <x-empty-model
                content="Aucune méthode référencée"
                :model="new \App\Models\Project"
                class="py-5"
                height="48"
            />
        @endif
    </x-slot>
</x-pages.projects.details-base>
