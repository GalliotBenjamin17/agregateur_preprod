<x-menu key="Administrateur">
    <x-slot:shortcut>
        <x-menu-shortcut modal-id="menu" text="Menu" />
    </x-slot:shortcut>

    <x-menu-tab content="Tableau de bord" :url="route('dashboard')" :is-active="request()->routeIs('dashboard')" />

    <x-menu-tab content="Projets" :url="route('projects.index')" :is-active="request()->routeIs('projects.*')" />


    @role('admin|local_admin')
        <x-menu-tab
            content="Finance"
            :url="route('donations.index')"
            :with-submenu="true"
            :is-active="request()->routeIs('donations.*', 'transactions.*', 'accountancy.*')"
        >
            <x-menu-card
                :url="route('donations.index')"
                :is-active="request()->routeIs('donations.*')"
                title="Contributions"
                description="Vue d'ensemble des contributions, etc."
                wire:navigate.hover
            >
                <x-slot:icon>
                    {!! \App\Helpers\IconHelper::donationsIcon() !!}
                </x-slot:icon>
            </x-menu-card>

            <x-menu-card
                :url="route('transactions.index')"
                :is-active="request()->routeIs('transactions.*')"
                title="Transactions"
                description="Vue d'ensemble des transactions en cours, etc."
                wire:navigate.hover
            >
                <x-slot:icon>
                    {!! \App\Helpers\IconHelper::transactionsIcon() !!}
                </x-slot:icon>
            </x-menu-card>

            <x-menu-card
                :url="route('accountancy.index')"
                :is-active="request()->routeIs('accountancy.*')"
                title="Exports financiers"
                description="Vue d'ensemble des contributions, etc."
                wire:navigate.hover
            >
                <x-slot:icon>
                    {!! \App\Helpers\IconHelper::donationsIcon() !!}
                </x-slot:icon>
            </x-menu-card>
        </x-menu-tab>

    @endrole

    @role('admin|local_admin|member|partner')

        <x-menu-tab
            content="Parties prenantes"
            :with-submenu="true"
            :is-active="request()->routeIs('users.*', 'organizations.*', 'partners.*')"
        >

            @role('admin|local_admin')
                <x-menu-card
                    :url="route('users.index')"
                    :is-active="request()->routeIs('users.*')"
                    title="Acteurs"
                    description="Vue d'ensemble des contributeurs, etc."
                    wire:navigate.hover
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::usersIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            @endrole

            @role('admin|local_admin|member')
                <x-menu-card
                    :url="route('organizations.index')"
                    :is-active="request()->routeIs('organizations.*')"
                    title="Organisations"
                    description="Vue d'ensemble des organisations."
                    wire:navigate.hover
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::organizationsIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            @endrole

            @role('admin|local_admin|partner')
                <x-menu-card
                    :url="route('partners.index')"
                    :is-active="request()->routeIs('.*')"
                    title="Partenaires"
                    description="Vue d'ensemble des partenaires."
                    wire:navigate.hover
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::partnersIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            @endrole

        </x-menu-tab>

    @endrole

    @role('admin|local_admin')

        <x-menu-tab
            content="Communication"
            :with-submenu="true"
            :is-active="request()->routeIs('news.*')"
        >
            @role('admin|local_admin')
                <x-menu-card
                    :url="route('news.index')"
                    :is-active="request()->routeIs('news.*')"
                    title="Actualités"
                    description="Vue d'ensemble des actualités."
                    wire:navigate.hover
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::newsIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            @endrole
        </x-menu-tab>

    @endrole
</x-menu>
