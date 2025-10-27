<x-pages.organizations.details-base
    :organization="$organization"
>
    <x-slot name="cardContent">

        <x-layouts.card
            name="Badges"
        >
            <x-slot:icon>
                {!! \App\Helpers\IconHelper::badgesIcon('sm') !!}
            </x-slot:icon>

            <x-slot:actions>
                <livewire:actions.organizations.badges-form
                    :organization="$organization"
                />
            </x-slot:actions>

            <div class="p-2.5 sm:p-[1rem] grid grid-cols-1 md:grid-cols-5 gap-x-16 gap-y-4">
                @forelse($organization->badges as $badge)
                    <img class="w-full tippy justify-center h-36 w-auto" data-tippy-content="{{ $badge->name }}" src="{{ Storage::url($badge->picture) }}">
                @empty
                    <x-empty-model
                        content="Aucun badge sur cette organisation"
                        :model="new \App\Models\Badge()"
                        class="col-span-5"
                        height="48"
                    />
                @endforelse
            </div>

        </x-layouts.card>

        <x-files-component :model="$organization" />
    </x-slot>
</x-pages.organizations.details-base>
