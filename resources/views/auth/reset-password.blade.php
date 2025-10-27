<x-guest-layout>
    <div class="flex min-h-full">
        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <img class="h-12 w-auto" src="{{ $tenant ? asset($tenant->logo) : asset('img/logos/cooperative-carbone/main.svg') }}">
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Mot de passe oublié</h2>
                </div>

                <div class="mt-8">
                    <div class="mt-6">
                        <div class="mb-4 text-sm text-gray-600">
                            <ul class="mt-5 list-disc list-inside text-sm">
                                <p class="font-semibold">Le mot de passe doit :</p>
                                <li>Faire plus de 8 caractères</li>
                                <li>Avoir a minima une lettre majuscule et minuscule</li>
                                <li>Contenir des chiffres, lettres et des caractères spéciaux</li>
                                <li>Ne pas être apparu dans une fuite de données au préalable (auquel cas le mot de passe sera instantané refusé après la validation)</li>
                            </ul>
                        </div>


                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div>
                                <x-label for="email" value="Adresse email : " required />
                                <x-input id="email"  type="email" name="email" :value="old('email', $request->email)" required autofocus />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div class="mt-4">
                                <x-label for="email" value="Mot de passe : " required />
                                <x-input id="password" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="mt-4">
                                <x-label for="email" value="Confirmation : " required />
                                <x-input id="password_confirmation"
                                              type="password"
                                              name="password_confirmation" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    Mettre à jour
                                </x-primary-button>
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
