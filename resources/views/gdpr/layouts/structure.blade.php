<x-guest-layout>
    <div class="bg-[#f6f7f9] min-h-screen">
        <div class="max-w-screen-lg mx-auto flex flex-col py-2 sm:py-12 px-2 sm:px-8 md:px-12 lg:px-16 space-y-4 sm:space-y-6 md:space-y-8">

            <div class="bg-white rounded-md border border-gray-300 shadow-sm px-4 md:px-8 py-6 md:py-10 space-y-8">
                <div>
                    <h1 class="text-2xl flex space-x-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-bold">
                            @hasSection('subTitle')
                                @yield('subTitle')
                            @else
                                Tableau de bord RGPD
                            @endif
                        </span>
                    </h1>
                    <div class="text-sm text-gray-600">
                        Vous pouvez accéder aux différentes sections pour satisfaire l'ensemble de vos requêtes liées aux lois relatives au RGPD.
                    </div>

                    @if(!request()->routeIs('gdpr.hub.index'))
                        <a href="{{ route('gdpr.hub.index') }}" class="mt-2 flex items-center space-x-2">
                            <x-icon.fleche_gauche class="h-5 w-5" />
                            <span>Retour au hub général</span>
                        </a>
                    @endif
                </div>

                @yield('gdpr-content')
            </div>
        </div>
    </div>

</x-guest-layout>
