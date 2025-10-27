<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">
        <x-layouts.card
            :group-name="$partnerProject->project->name"
            :name="$partnerProject->partner->name"
        >

            <x-slot:icon>
                {!! \App\Helpers\IconHelper::partnersIcon() !!}
            </x-slot:icon>

            <livewire:forms.project-partners.details-form :partner-project="$partnerProject" />
        </x-layouts.card>


        <x-layouts.card
            name="Paiements initiés"
        >
            <x-slot:actions>
                <livewire:actions.partner-project-payments.create-form :partner-project="$partnerProject" />
            </x-slot:actions>

            <livewire:tables.partner-project-payments.index-table :partner-project="$partnerProject" />

        </x-layouts.card>

    </x-slot>
</x-pages.projects.details-base>
