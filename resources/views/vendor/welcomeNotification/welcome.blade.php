@extends('welcomeNotification::base')

@section('content')
    <div class="max-w-screen-lg mx-auto flex flex-col py-2 sm:py-12 px-2 sm:px-8 md:px-12 lg:px-16 space-y-4 sm:space-y-6 md:space-y-8">
        <div>
            <img class="h-14 w-auto" src="{{ asset($logo) }}" alt="logo">
        </div>

        @if($welcomeExplanations)
            <div style="background-color: {{ $accentColor }}; color: {{ $textColor }}" class="block w-full group rounded-[16px] shadow-sm overflow-hidden border border-gray-300 overflow-hidden flex items-start">
                <div class="w-32 h-32 hidden md:flex flex-shrink-0 h-auto">
                    <img class="w-32 h-32 scale-110 object-cover object-right" src="{{ asset('img/illustrations/squares/voice.png') }}">
                </div>
                <div class="text-[20px] py-3.5 px-8">
                    <h3 class="font-semibold">{{ $welcomeExplanations }}</h3>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-md shadow-sm border border-gray-300 px-4 md:px-8 py-6 md:py-10 space-y-5">
            <div>
                <h1 class="text-2xl flex  space-x-2 items-center">
                    <span class="font-bold">
                        Première connexion
                    </span>
                </h1>
            </div>
            <div>
                <form method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}"/>

                    <div class="">
                        <x-information-alert
                            type="info"
                            title="Information de sécurité. "
                            message="Ce mot de passe vous permettra de vous connecter de manière sécurisée à la plateforme. Ne partagez pas ce mot de passe avec d'autres personnes. Chaque compte est strictement personnel."
                        />
                        <ul class="mt-5 list-disc list-inside text-sm">
                            <p class="font-semibold">Le mot de passe doit :</p>
                            <li>Faire plus de 8 caractères</li>
                            <li>Avoir a minima une lettre majuscule et minuscule</li>
                            <li>Contenir des chiffres, lettres et des caractères spéciaux</li>
                            <li>Ne pas être apparu dans une fuite de données au préalable (auquel cas le mot de passe sera instantané refusé après la validation)</li>
                        </ul>

                        <div class="mt-5">
                            <x-label for="password" value="Mot de passe" />
                            <x-input id="password" placeholder="··········" type="password" name="password" required autocomplete="new-password" :error="$errors->has('password')"/>
                            <x-error-message :errors="$errors" key="password" />
                        </div>

                        <div class="mt-5">
                            <x-label for="password" value="Confirmation" />
                            <x-input class="mt-1" id="password-confirm" placeholder="··········" type="password" name="password_confirmation" required autocomplete="new-password" :error="$errors->has('password')"/>
                            <x-error-message :errors="$errors" key="password_confirmation" />
                        </div>

                        <div class="flex items-center space-x-2 mt-5">
                            <div class="relative bg-gray-50 rounded-md border border-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-gray-500 hover:bg-gray-100">
                                <p class="absolute -top-2 -left-2 text-xs bg-yellow-500 text-white rounded-full px-2 py-0.5">
                                    Consentement RGPD
                                </p>
                                <div class="flex items-start space-x-4 px-4 py-2 mt-5">
                                    <div class="flex-shrink-0">
                                        <img class="h-10 w-10" src="{{ asset("img/illustrations/squares/list.png") }}" alt="">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Consentement à l'utilisation de vos données personnelles</p>
                                            <div class="text-sm text-gray-500 prose prose-sm no-underline	">
                                                {!! $gdprExplanations !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="relative flex items-start">
                                <div class="flex h-5 items-center">
                                    <input id="conditions" name="conditions" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="conditions" class="font-medium text-gray-700">J'accepte les conditions d'utilisations de mes données personnelles, les CGU et la PCD.</label>
                                    <p>
                                        <a href="{{ $tenant?->cgu ?? '#!' }}" target="_blank" class="text-blue-500 hover:underline">
                                            Conditions générales d'utilisation
                                        </a>
                                        et la
                                        <a href="{{ $tenant?->data_policy_url ?? '#!' }}" target="_blank" class="text-blue-500 hover:underline">
                                            politique de confidentialité des données
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>


                        <div class="flex justify-end mt-5">
                            <x-button type="success" size="lg" icon submit>
                                <span>Mettre à jour le mot de passe</span>
                                <x-icon.chevron_droite class="h-5 w-5" />
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <div class="max-w-5xl mx-auto  py-10">
            <p class="text-center text-sm leading-6 text-slate-500">{{ now()->year }} — {{ $name }}. Tous droits réservés.</p>
            <div class="mt-4 flex items-center justify-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <a href="{{ $tenant?->cgu ?? '#!' }}">Conditions générales d'utilisation</a>
            </div>
        </div>
    </div>
@endsection
