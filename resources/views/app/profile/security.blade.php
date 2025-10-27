<x-pages.profile.details-base
    page-name="Sécurité"
>
    <x-slot name="cardContent">
        <div class="mt-5">
            <h2 class="font-semibold text-xl">Mot de passe</h2>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="flex">
                <form method="POST" action="{{ route('profile.reset-password') }}">
                    @csrf
                    <input class="hidden" name="email" value="{{ $user->email }}">
                    <input class="hidden" name="back" value="on">
                    <x-button size="lg" class="mt-2" submit type="warning" icon>
                        <span>Recevoir un email de réinitialisation</span>
                        <x-icon.chevron_droite class="h-5 w-5" />
                    </x-button>
                </form>
            </div>
        </div>

        <div class="mt-20">
            <h2 class="font-semibold text-xl">Dernières connexions</h2>
            <div>
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($authEvents as $authEvent)
                        <li class="py-2 ">
                            <div class="flex space-x-3">
                                <div class="flex-1 space-y-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-[14px] font-medium">{{ $authEvent->authenticatable?->name ?? "Utilisateur inconnu" }}</h3>
                                        <p class="text-sm text-gray-500">@datetime($authEvent->login_at)</p>
                                    </div>
                                    <p class="text-[13px] text-gray-500">{{ $authEvent->ip_address }}, {{ $authEvent?->location['city'] ?? '-' }}</p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </x-slot>
</x-pages.profile.details-base>
