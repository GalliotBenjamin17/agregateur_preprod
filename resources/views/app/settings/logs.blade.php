<x-pages.settings.details-base
    page-name="Logs de connexion"
>
    <x-slot name="cardContent">
        <div class="mt-5">
            <div>
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($authEvents as $authEvent)
                        <li class="py-2 px-2">
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
</x-pages.settings.details-base>
