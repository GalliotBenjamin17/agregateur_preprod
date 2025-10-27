<x-guest-layout>
    <div class="flex min-h-full">
        <div @class([
            "flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:px-10 xl:px-15",
            "lg:flex-none" => !request()->has('fullPage')
        ])>
            <div class="mx-auto w-full max-w-2xl lg:w-[32rem]">
                <div>
                    <img class="h-12 w-auto" src="{{ $tenant ? asset($tenant->logo) : asset('img/logos/cooperative-carbone/main.svg') }}">
                    <h2 class="mt-6 text-3xl font-semibold tracking-tight text-gray-800">Créer un compte</h2>

                </div>

                <div class="mt-4">
                    <livewire:forms.auth.register-form :tenant="$tenant" />
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
