<x-layouts.card
        group-name="Objectifs de développement durable"
        name="ODD affiliés au projet"
        :thin-padding="true"
        {{ $attributes }}>
    <x-slot:icon>
        {!! \App\Helpers\IconHelper::sustainableDevelopmentGoalsIcon(size: 'lg') !!}
    </x-slot:icon>

    <x-slot:actions>
        @if(!$project->hasFormFieldsDisabled())
            <livewire:actions.odd.store-to-project :project="$project" :redirect-url="url()->current()" />
        @endif
    </x-slot:actions>

    <x-slot:content>
        <div class="p-2.5 sm:p-[1rem] flex items-center flex-wrap gap-x-5 gap-y-2">
            @forelse($project->sustainableDevelopmentGoals as $sustainableDevelopmentGoal)
                <img class="rounded-md h-16 cursor-help tippy" src="{{ asset($sustainableDevelopmentGoal->image) }}"
                     data-tippy-content="<center>{{ $sustainableDevelopmentGoal->description }}</center>"/>
            @empty
                <x-empty-model
                        content="Aucun ODD référencé"
                        :model="new \App\Models\SustainableDevelopmentGoals()"
                        class="col-span-4"
                        height="48"
                />
            @endforelse
        </div>
    </x-slot:content>
</x-layouts.card>
