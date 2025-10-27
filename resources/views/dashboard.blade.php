<x-app-layout>
    <x-slot name="content">
        @section('title', "Tableau de bord")

        @role('admin|local_admin')
            <x-layouts.card
                group-name="Acteurs, Projets & Contributions"
                name="Statistiques"
            >
                <x-slot:icon>
                    <div class="h-[40px] w-[40px] bg-[#ffd700] rounded-md flex ">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 mx-auto my-auto text-white">
                            <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" />
                        </svg>
                    </div>
                </x-slot:icon>

                <x-slot:content>
                    <div class="isolate bg-white p-3 mx-auto grid max-w-md grid-cols-1 gap-5 md:max-w-2xl md:grid-cols-2 lg:max-w-4xl xl:mx-0 xl:max-w-none xl:grid-cols-4">
                        <!-- Acteurs -->
                        <div class="rounded-md border border-gray-300 p-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-gray-700 text-3xl font-extrabold leading-8">
                                    Acteurs
                                </h2>

                                <form method="POST" action="{{ route('users.export') }}">
                                    @csrf
                                    <x-button icon submit>
                                        <x-icon.telecharger class="h-5 w-5 text-gray-500" />
                                    </x-button>
                                </form>
                            </div>

                            <div class="mt-5 space-y-2">
                                @foreach(\App\Enums\Roles::toDashboard() as $key => $value)
                                    <x-card-statistics
                                        :title="$value . 's'"
                                        :number="$roles->where('name', $key)->first()?->users_count"
                                    />
                                @endforeach
                                <x-card-statistics
                                    title="Partenaires"
                                    :number="\App\Models\Partner::count()"
                                />

                            </div>
                        </div>

                        <!-- Projets -->
                        <div class="rounded-md border border-gray-300 p-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-gray-700 text-3xl font-extrabold leading-8">
                                    Projets
                                </h2>


                                <x-dropdown dropdown-position="left" class="inline-flex items-center justify-center py-1 pl-3 pr-10 text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                                    <x-slot:trigger>
                                        <span>Exports</span>

                                        <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                    </x-slot:trigger>


                                    <form id="export-project-all" method="POST" action="{{ route('projects.export') }}">
                                        @csrf
                                        <x-dropdown.item href="#" onclick="document.getElementById('export-project-all').submit()">
                                            <x-slot:icon>
                                                <x-icon.telecharger class="h-4 w-4 mr-2 text-gray-500" />
                                            </x-slot:icon>
                                            Tous
                                        </x-dropdown.item>
                                    </form>

                                    <form id="export-project-sub-projects" method="POST" action="{{ route('projects.export.sub-projects') }}">
                                        @csrf
                                        <x-dropdown.item href="#" onclick="document.getElementById('export-project-sub-projects').submit()">
                                            <x-slot:icon>
                                                <x-icon.telecharger class="h-4 w-4 text-gray-500" />
                                            </x-slot:icon>
                                            Sous-projets
                                        </x-dropdown.item>
                                    </form>
                                </x-dropdown>
                            </div>

                            <div class="mt-5 space-y-2">
                                <x-card-statistics
                                    title="Total"
                                    :number="$projects->sum()"
                                />
                                @foreach(config('values.states.projets.name') as $key => $value)
                                    <x-card-statistics
                                        :title="$value"
                                        :number="$projects[$key] ?? 0"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <!-- Finance -->
                        <div class="rounded-md border border-gray-300 p-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-gray-700 text-3xl font-extrabold leading-8">
                                    Finance
                                </h2>


                                <x-dropdown dropdown-position="left" class="inline-flex items-center justify-center py-1 pl-3 pr-10 text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                                    <x-slot:trigger>
                                        <span>Exports</span>

                                        <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                    </x-slot:trigger>


                                    <form id="export-donations-all" method="POST" action="{{ route('donations.export') }}">
                                        @csrf
                                        <x-dropdown.item href="#" onclick="document.getElementById('export-donations-all').submit()">
                                            <x-slot:icon>
                                                <x-icon.telecharger class="h-4 w-4 mr-2 text-gray-500" />
                                            </x-slot:icon>
                                            Contributions
                                        </x-dropdown.item>
                                    </form>

                                    <form id="export-donations-splits-all" method="POST" action="{{ route('donations.export-splits') }}">
                                        @csrf
                                        <x-dropdown.item href="#" onclick="document.getElementById('export-donations-splits-all').submit()">
                                            <x-slot:icon>
                                                <x-icon.telecharger class="h-4 w-4 text-gray-500" />
                                            </x-slot:icon>
                                            Fléchages (détails)
                                        </x-dropdown.item>
                                    </form>
                                </x-dropdown>


                            </div>
                            <div class="mt-5 space-y-2">
                                <x-card-statistics
                                    title="Contributions récoltées"
                                    sub-title=" contributions"
                                    :number="format($donationTotalCount, 0)"
                                />
                                <x-card-statistics
                                    title="Contributions récoltées"
                                    :number="format($donationTotalAmount) .  ' € TTC'"
                                />
                                <x-card-statistics
                                    title="Contributions fléchées"
                                    :number="format($donationDoneAmount) .  ' € TTC'"
                                />
                                <x-card-statistics
                                    title="Contributions non-fléchées"
                                    :number="format($donationWaitingAmount) .  ' €TTC'"
                                />
                                <x-card-statistics
                                    title="Contributions depuis particuliers"
                                    :number="format($donationFromUsers) .  ' € TTC'"
                                />
                                <x-card-statistics
                                    title="Contributions depuis organisation"
                                    :number="format($donationFromOrganizations) .  ' € TTC'"
                                />
                                <x-card-statistics
                                    title="Contributions autres supports (borne, etc)"
                                    :number="format($donationFromOthers) .  ' € TTC'"
                                />
                            </div>
                        </div>

                        <!-- Carbone -->
                        <div class="rounded-md border border-gray-300 p-4">
                            <h2 class="text-gray-700 text-3xl font-extrabold leading-8">
                                Carbone
                            </h2>
                            <div class="mt-5 space-y-2">
                                <x-card-statistics
                                    title="Cible tCo2"
                                    sub-title=" tCo2"
                                    :number="format($targetCo2)"
                                />
                                <x-card-statistics
                                    title="Réalisé tCo2"
                                    sub-title=" tCo2"
                                    :number="format($doneCo2)"
                                />
                                <x-card-statistics
                                    title="Financé tCo2"
                                    sub-title=" TTC"
                                    :number="format($doneCo2Amount, 2) .  ' €'"
                                />
                            </div>
                        </div>
                    </div>
                </x-slot:content>
            </x-layouts.card>
        @endrole

        @role('member')
            <x-layouts.card
                group-name="Tableau de bord"
                name="Organisations"
            >
                <x-slot:icon>
                    <div class="h-[40px] w-[40px] bg-[#ffd700] rounded-md flex ">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 mx-auto my-auto text-white">
                            <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" />
                        </svg>
                    </div>
                </x-slot:icon>

                <x-slot:content>
                    <livewire:tables.organizations.index-table />
                </x-slot:content>
            </x-layouts.card>
        @endrole

        @role('referent|auditor|sponsor|partner')
            <x-layouts.card
                group-name="Tableau de bord"
                name="Projets"
            >
                <x-slot:icon>
                    <div class="h-[40px] w-[40px] bg-[#ffd700] rounded-md flex ">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 mx-auto my-auto text-white">
                            <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" />
                        </svg>
                    </div>
                </x-slot:icon>

                <x-slot:content>
                    <livewire:tables.projects.index-table />
                </x-slot:content>
            </x-layouts.card>
        @endrole
    </x-slot>
</x-app-layout>
