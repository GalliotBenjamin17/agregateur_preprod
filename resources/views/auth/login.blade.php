<x-guest-layout>
    <div class="flex min-h-full">
        <div @class([
            "flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24",
            "lg:flex-none" => !request()->has('fullPage')
        ])>
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <img class="h-12 w-auto" src="{{ $tenant ? asset($tenant->logo) : asset('img/logos/cooperative-carbone/main.svg') }}">
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Connexion</h2>
                </div>

                <div class="mt-8">
                    <div>
                        <div class="relative mt-6">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-white px-2 text-gray-500">IDENTIFIANTS</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <form action="{{ route('login') }}" method="POST" class="space-y-6">
                            <div>
                                <x-label for="email" value="Adresse email" />
                                <x-input class="mt-1" id="email" name="email" type="email" autocomplete="email" required autofocus />
                            </div>

                            <div class="space-y-1">
                                <x-label for="password" value="Mot de passe" />
                                <x-input clas="mt-1" id="password" name="password" type="password" autocomplete="current-password" required />
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">Se souvenir de moi</label>
                                </div>

                                <div class="text-sm">
                                    <a href="{{ route('password.email') }}" class="font-medium text-mc-donalds-green">Mot de passe oublié ?</a>
                                </div>
                            </div>

                            <div>
                                @csrf
                                <x-button type="success" class="w-full !bg-[#244999]"  size="lg" submit>
                                    Se connecter
                                </x-button>

                                @if($tenant)
                                    <a href="{{ route('tenant.register', ['tenant' => $tenant]) }}" class="flex items-center justify-center mt-5 text-[#244999] font-medium text-sm">
                                        <span>Pas de compte ? En créer un</span>
                                        <x-icon.chevron_droite class="h-5 w-5" />
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div @class([
            "relative hidden w-0 flex-1 lg:block",
            "!hidden lg:!hidden" => request()->has('fullPage')
        ])>
            @if($tenant and $tenant->login_image)
                <img class="absolute bg-gray-100 inset-0 h-full w-full object-cover" src="{{ asset($tenant->login_image) }}" alt="">
            @else
                <img class="absolute bg-blue-100 inset-0 h-full w-full object-contain p-10" src="{{ asset('img/illustrations/cooperative-carbone/Coop_confiance_without_logos.svg') }}" alt="">
            @endif
        </div>
    </div>
</x-guest-layout>
