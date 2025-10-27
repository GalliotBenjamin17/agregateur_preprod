<!DOCTYPE html>
<html lang="fr-FR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('layouts.seo')

    @vite([
        'resources/css/app.css',
        'resources/scss/bootstrap.scss',
        'resources/js/app.js'
    ])

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />

    @filamentStyles
    <link href="{{ asset('css/mobiscroll.javascript.min.css') }}" rel="stylesheet" type="text/css">
    @stack('styles')


    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="relative antialiased h-full bg-gray-100">

    <div class="relative bg-white">
        <x-layouts.top-bar />
        <x-layouts.menu />
    </div>

    <div class="relative overflow-y-auto" style="height: calc(100vh - 99px); scrollbar-width: thin;">
        <div class="relative">
            <!--content app-->
            @isset($content)
                <div class="p-1.5 sm:p-3 space-y-3">
                    {{ $content }}
                </div>
            @endisset
        </div>
    </div>


    @isset($modals)
        {{ $modals }}
    @endisset

    <x-modal id="menu" size="lg">
        <x-modal.header>
            <div>
                <div class="font-semibold text-gray-700">
                    Menu
                </div>
            </div>
            <x-modal.close-button/>
        </x-modal.header>

        <x-modal.body class="grid grid-cols-1 gap-2 sm:gap-3 sm:grid-cols-2 border-t border-gray-300">
            <div class="col-span-2">
                <x-menu-card
                    :url="route('dashboard')"
                    :is-active="request()->routeIs('dashboard')"
                    title="Tableau de bord"
                    description="Vue d'ensemble des statistiques."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::activitiesIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            </div>

            <x-menu-card
                :url="route('projects.index')"
                :is-active="request()->routeIs('projects.*')"
                title="Projets"
                description="Liste des projets référencés."
            >
                <x-slot:icon>
                    {!! \App\Helpers\IconHelper::projectIcon() !!}
                </x-slot:icon>
            </x-menu-card>

            @role('admin|local_admin')
                <x-menu-card
                    :url="route('news.index')"
                    :is-active="request()->routeIs('news.*')"
                    title="Actualités"
                    description="Liste des actualités."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::newsIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            @endrole

            @role('admin|local_admin|member')
                <x-menu-card
                    :url="route('organizations.index')"
                    :is-active="request()->routeIs('organizations.*')"
                    title="Organisations"
                    description="Vue d'ensemble des entreprises, partenaires, etc."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::organizationsIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            @endrole

            @role('admin|local_admin')
                <x-menu-card
                    :url="route('users.index')"
                    :is-active="request()->routeIs('users.*')"
                    title="Acteurs"
                    description="Vue d'ensemble des personnes inscrites."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::usersIcon() !!}
                    </x-slot:icon>
                </x-menu-card>

                <x-menu-card
                    :url="route('partners.index')"
                    :is-active="request()->routeIs('partners.*')"
                    title="Partenaires"
                    description="Liste des partenaires de la plateforme."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::partnersIcon() !!}
                    </x-slot:icon>
                </x-menu-card>

                <x-menu-card
                    :url="route('donations.index')"
                    :is-active="request()->routeIs('donations.*')"
                    title="Contributions"
                    description="Liste des contributions."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::donationsIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
                <x-menu-card
                    :url="route('transactions.index')"
                    :is-active="request()->routeIs('transactions.*')"
                    title="Transactions"
                    description="Liste des transactions en fonction de leur statut."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::transactionsIcon() !!}
                    </x-slot:icon>
                </x-menu-card>

                <x-menu-card
                    :url="route('accountancy.index')"
                    :is-active="request()->routeIs('accountancy.*')"
                    title="Exports financiers"
                    description="Vue d'ensemble des exports comptables."
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::donationsIcon() !!}
                    </x-slot:icon>
                </x-menu-card>
            @endrole
        </x-modal.body>

        <x-modal.footer>
            <x-button data-bs-dismiss="modal">
                Fermer
            </x-button>
        </x-modal.footer>
    </x-modal>

    <x-modal id="all_reminders">
        <x-modal.header>
            <div>
                <div class="font-semibold text-gray-700">
                    Tous les rappels à venir
                </div>
            </div>
            <x-modal.close-button/>
        </x-modal.header>

        <x-modal.body class="border-t border-gray-300 !p-0">
            <ul role="list" class="divide-y w-full divide-gray-200">
                @forelse($reminders as $reminder)
                    <li class="relative w-full py-2 px-6 group flex items-start justify-between tippy" data-tippy-content="Rappel le {{ $reminder->reminder_at->format('d/m') }}, notification à {{ $reminder->notification_at->format('d/m') }}">
                        <div class="w-full flex items-start justify-between space-x-3 group-hover:blur-sm">
                            <div class="flex-1 space-y-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-[14px] font-medium">{{ $reminder->content }}</h3>
                                    <p class="text-sm text-gray-500">{{ $reminder->reminder_at->format('d/m') }}</p>
                                </div>
                                <p class="text-[13px] text-gray-500">
                                    Sur {{ $reminder->related?->name }} par
                                    {{ $reminder->createdBy?->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 hidden group-hover:absolute group-hover:right-5 group-hover:block">
                            <form method="POST" action="{{ route('reminder.delete', ['reminder' => $reminder]) }}" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <x-button submit type="danger" icon>
                                    <x-icon.poubelle class="h-5 w-5" />
                                </x-button>
                            </form>
                            <x-button href="{{ method_exists($reminder->related, 'redirectRouter') ? $reminder->related->redirectRouter() : null }}" target="_blank" type="info" class="inline-flex" icon>
                                <x-icon.chevron_droite class="h-5 w-5" />
                            </x-button>
                        </div>
                    </li>
                @empty
                    <x-empty-model
                        content="Aucun rappel à venir"
                        :model="new \App\Models\Reminder()"
                        class="col-span-4 py-3"
                        height="48"
                    />
                @endforelse
            </ul>
        </x-modal.body>
    </x-modal>

    <script src="{{ asset('js/mobiscroll.javascript.min.js') }}"></script>
    @stack('scripts')
    @filamentScripts
    @livewire('notifications')
    @vite(['resources/js/bootstrap.js'])

    <script async>
        @if($errors->any())
            @foreach($errors->all() as $error)
                setTimeout(function () {
                    new FilamentNotification().title('Une erreur est survenue').body("{{ $error }}").danger().send();
                }, 500);
            @endforeach
        @endif

        @if(Session::has('alert'))
            setTimeout(function () {
                new FilamentNotification().title("{{ Session::get('alert') }}").danger().send();
            }, 500);
        @endif

        @if(Session::has('success'))
            setTimeout(function () {
                new FilamentNotification().title("{{ Session::get('success') }}").success().send();
            }, 500);
        @endif

        @if(Session::has('info'))
            setTimeout(function () {
                new FilamentNotification().title("{{ Session::get('info') }}").warning().send();
            }, 500);
        @endif
    </script>
</body>
</html>
