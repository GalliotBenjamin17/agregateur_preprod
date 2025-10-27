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
                            Vous avez oublié votre mot de passe ? Aucun problème. Indiquez-nous simplement votre adresse électronique et nous vous enverrons un lien de réinitialisation du mot de passe qui vous permettra d'en choisir un nouveau.
                        </div>

                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />

                                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />

                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    Envoyer
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative hidden w-0 flex-1 lg:block">
            @if($tenant and $tenant->login_image)
                <img class="absolute bg-gray-100 inset-0 h-full w-full object-cover" src="{{ asset($tenant->login_image) }}" alt="">
            @else
                <img class="absolute bg-blue-100 inset-0 h-full w-full object-contain p-10" src="{{ asset('img/illustrations/cooperative-carbone/Coop_confiance_without_logos.svg') }}" alt="">
            @endif
        </div>
    </div>
</x-guest-layout>
