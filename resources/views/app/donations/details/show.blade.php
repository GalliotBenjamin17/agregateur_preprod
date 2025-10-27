<x-pages.donations.details-base
    :donation="$donation"
>
    <x-slot name="cardContent">

        <x-files-component :model="$donation" />

    </x-slot>

    <x-slot name="colContent">
        <div class="col-span-4 space-y-2 sm:space-y-3">
            <x-comments-card :model="$donation" />
            <x-activities-model :model="$donation" />
        </div>
    </x-slot>
</x-pages.donations.details-base>
