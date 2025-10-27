<x-pages.profile.details-base
    page-name="Données"
>
    <x-slot name="cardContent">
        <div class="mt-5">
            <h2 class="font-semibold text-xl">HUB RGPD</h2>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="my-5">
                <x-information-alert
                    type="info"
                    title="Gérez vos donées directement depuis le HUB RGPD. "
                    message="Nous avons déplacé et centralisé cette section dans le hub RGPD pour vous apporter la meilleure expérience possible de gestion de vos données."
                />
            </div>

            <div class="flex">
                <x-button size="lg" target="_blank" href="{{ route('gdpr.hub.index') }}" class="mt-2" submit type="success" icon>
                    <span>Accéder au Hub RGPD</span>
                    <x-icon.chevron_droite class="h-5 w-5" />
                </x-button>
            </div>
        </div>
    </x-slot>
</x-pages.profile.details-base>
