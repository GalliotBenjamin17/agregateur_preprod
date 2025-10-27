<x-layouts.card
    group-name="Activités"
    name="Dernières activités"
    :thin-padding="true"
>
    <x-slot:icon>
        {!! \App\Helpers\IconHelper::activitiesIcon(size: 'lg') !!}
    </x-slot:icon>

    <div>
        <ul role="list" class="divide-y divide-gray-200 max-h-[250px] overflow-y-auto">
            @forelse($activities as $activity)
                <a @if($activity->properties['url'] ?? url()->current() == url()->current()) href="#!" @else href="{{ $activity->properties['url'] ?? '#!' }}" target="_blank" @endif class="block py-2 px-6 hover:bg-gray-50 tippy" data-tippy-content="Effectué par {{ $activity->causer?->name ?? 'un utilisateur supprimé' }}">
                    <div class="flex space-x-3">
                        <div class="flex-1 space-y-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-[14px] font-medium">{{ $activity->description }}</h3>
                                <p class="text-sm text-gray-500">@datetime($activity->created_at)</p>
                            </div>
                            @isset($activity->properties['description'])
                                <p class="text-[13px] text-gray-500">{{ $activity->properties['description'] }}</p>
                            @endisset
                        </div>
                    </div>
                </a>
            @empty
                <x-empty-model
                    content="Aucune activité référencée"
                    :model="new Spatie\Activitylog\Models\Activity()"
                    class="py-5"
                    height="48"
                />
            @endforelse
        </ul>
    </div>
</x-layouts.card>
