<div>
    <x-button type="button" icon class="relative" data-bs-toggle="modal" data-bs-target="#show_reminders_{{ $model->id }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="-ml-1 mr-2 h-5 w-5 text-gray-400">
            <path d="M4.214 3.227a.75.75 0 00-1.156-.956 8.97 8.97 0 00-1.856 3.826.75.75 0 001.466.316 7.47 7.47 0 011.546-3.186zM16.942 2.271a.75.75 0 00-1.157.956 7.47 7.47 0 011.547 3.186.75.75 0 001.466-.316 8.971 8.971 0 00-1.856-3.826z" />
            <path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 00.515 1.076 32.94 32.94 0 003.256.508 3.5 3.5 0 006.972 0 32.933 32.933 0 003.256-.508.75.75 0 00.515-1.076A11.448 11.448 0 0116 8a6 6 0 00-6-6zm0 14.5a2 2 0 01-1.95-1.557 33.54 33.54 0 003.9 0A2 2 0 0110 16.5z" clip-rule="evenodd" />
        </svg>
        <span>Rappels</span>
        <span class="absolute text-center leading-4 -right-1.5 -top-2 h-3.5 w-3.5 bg-red-500 text-white text-[.65rem] rounded-full ring-1 ring-gray-300">
            {{ sizeof($reminders) }}
        </span>
    </x-button>

    <x-modal id="show_reminders_{{ $model->id }}">
        <x-modal.header>
            <div class="flex items-start space-x-2">
                <div class="flex-shrink-0">
                    {!! \App\Helpers\IconHelper::viewIcon(model: $model) !!}
                </div>
                <div class="leading-5">
                    <div class="font-semibold">Rappels à venir</div>
                    <div class="text-gray-500">{{ $model->name }}</div>
                </div>
            </div>
            <x-modal.close-button/>
        </x-modal.header>

        <x-modal.body class="border-t border-gray-300 rounded-md overflow-hidden !p-0">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($reminders as $reminder)
                    <li class="relative w-full py-2 px-6 group flex items-start justify-between tippy" data-tippy-content="Rappel le {{ $reminder->reminder_at->format('d/m') }}, notification à {{ $reminder->notification_at->format('d/m') }}">
                        <div class="w-full flex items-start justify-between space-x-3 group-hover:blur-sm">
                            <div class="flex-1 space-y-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-[14px] font-medium">{{ $reminder->content }}</h3>
                                    <p class="text-sm text-gray-500">{{ $reminder->reminder_at->format('d/m') }}</p>
                                </div>
                                <p class="text-[13px] text-gray-500">
                                    Sur {{ $reminder->related?->name }} par
                                    {{ $reminder->createdBy?->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 hidden group-hover:absolute group-hover:right-5 group-hover:block">
                            <form method="POST" action="{{ route('reminder.delete', ['reminder' => $reminder]) }}" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <x-button submit type="danger" icon>
                                    <x-icon.poubelle class="h-5 w-5" />
                                </x-button>
                            </form>
                            <x-button href="{{ method_exists($reminder->related, 'redirectRouter') ? $reminder->related->redirectRouter() : null }}" target="_blank" type="info" class="inline-flex" icon>
                                <x-icon.chevron_droite class="h-5 w-5" />
                            </x-button>
                        </div>
                    </li>
                @empty
                    <x-empty-model
                        content="Aucun rappel à venir"
                        :model="new \App\Models\Reminder()"
                        class="col-span-4 py-5"
                        height="48"
                    />
                @endforelse
            </ul>
            <form method="POST" action="{{ route('reminder.store') }}" class="bg-gray-50 p-2.5 sm:p-[1rem]">
                <input class="hidden" name="related_id" value="{{ $model->id }}">
                <input class="hidden" name="related_type" value="{{ get_class($model) }}">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <x-label value="Contenu du rappel :" required />
                        <x-textarea placeholder="Envoyer document de ..." required name="content">{{old('content')}}</x-textarea>
                    </div>
                    <div class="col-span-2 grid grid-cols-2 gap-3">
                        <div>
                            <x-label value="Date du rappel :" required />
                            <x-input min="{{ now()->format('Y-m-d') }}" type="date" placeholder="d/m/Y" value="{{ old('reminder_at') }}" required name="reminder_at" />
                        </div>
                        <div>
                            <x-label value="Me le notifier le :" />
                            <x-input min="{{ now()->format('Y-m-d') }}" type="date" placeholder="d/m/Y" value="{{ old('notification_at') }}" name="notification_at" />
                        </div>
                    </div>
                </div>
                <div class="flex items-enter space-x-2 justify-end mt-4">
                    <x-button data-bs-dismiss="modal">
                        Fermer
                    </x-button>
                    <x-button submit type="success">
                        Ajouter le rappel
                    </x-button>
                </div>
            </form>
        </x-modal.body>
    </x-modal>
</div>
