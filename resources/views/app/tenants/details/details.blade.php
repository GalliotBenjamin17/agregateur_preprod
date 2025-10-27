<x-pages.tenants.details-base
    :tenant="$tenant"
>
    <x-slot name="cardContent">

    </x-slot>

    <x-slot name="colContent">
        <div class="col-span-4 space-y-2 sm:space-y-3">
            <x-comments-card :model="$tenant" />
            <x-activities-model :model="$tenant" />
        </div>
    </x-slot>
</x-pages.tenants.details-base>
