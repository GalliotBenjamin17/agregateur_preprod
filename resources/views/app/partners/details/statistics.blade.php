<x-pages.partners.details-base
    :partner="$partner"
>
    <x-slot name="cardContent">

        <div class="grid grid-cols-3 gap-5">
            <x-card-statistics
                title="Projets"
                :number="format($projectsCount, 0)"
            />

            <x-card-statistics
                title="Total commissions versée"
                :number="format($paymentsDoneAmount)"
                sub-title=" €"
            />
        </div>

    </x-slot>
</x-pages.partners.details-base>
