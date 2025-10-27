@props([
    /** @var \App\Models\Exchange */
    'exchange'
])

<div {{ $attributes->class(['space-y-3']) }}>
    <div class="mt-3">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <img class="inline-block h-10 w-10 rounded-full bg-white"
                     src="{{ asset(\Illuminate\Support\Facades\Auth::user()->avatar) }}" alt="">
            </div>
            <div class="min-w-0 flex-1">
                <form method="post"
                      action="{{ route('permanence.exchanges.messages.store', ['exchange' => $exchange->slug]) }}"
                      class="relative">
                    @csrf
                    <div class="overflow-hidden rounded-lg border border-gray-300 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                        <label for="comment" class="sr-only">Publier un nouveau message</label>
                        <textarea rows="3" name="content" id="comment"
                                  class="block w-full resize-none border-0 py-3 focus:ring-0 sm:text-sm"
                                  placeholder="Publier un nouveau message"></textarea>
                        <div class="py-2" aria-hidden="true">
                            <div class="py-px">
                                <div class="h-6"></div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 flex justify-between py-2 pl-3 pr-2">
                        <div class="flex items-center space-x-2 text-sm italic text-gray-500">
                            <x-icon.check_info class="h-4 w-4"/>
                            <span>Restez courtois et polis dans vos propos.</span>
                        </div>
                        <div class="flex-shrink-0 ml-auto">
                            <x-button submit class="text-white" icon type="success">
                                <span>Répondre</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                     class="w-5 h-5">
                                    <path fill-rule="evenodd"
                                          d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="relative py-5">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-white px-2  text-gray-500">Tous les messages</span>
        </div>
    </div>


    @php
        $oldDate = \Carbon\Carbon::createFromFormat("d/m/Y", "01/01/2000")
    @endphp
    @foreach($exchange->exchangeMessages as $exchangeMessage)
        <div>
            @if($exchange->files->where('created_at', '<', $oldDate)->where('created_at', '>', $exchangeMessage->created_at)->count() > 0 or ($loop->first and $exchange->files->where('created_at', '>', $exchangeMessage->created_at)->count() > 0))
                <div class="flex items-center gap-3 mb-1">
                    @php
                        if ($loop->first) {
                            $files = $exchange->files->where('created_at', '>', $exchangeMessage->created_at);
                        }
                        else {
                            $files = $exchange->files->where('created_at', '<', $oldDate)->where('created_at', '>', $exchangeMessage->created_at);
                        }
                    @endphp
                    @foreach($files as $file)
                        <div class="relative flex items-center space-x-2 px-3 py-2 bg-white rounded-md border border-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-gray-500 hover:bg-gray-100">
                            <div class="flex-shrink-0">
                                <img class="h-4 hidden sm:block" src="/img/extensions/{{ $file['extension'] }}.png"
                                     onerror="this.onerror=null;this.src='{{ asset('img/extensions/doc.png') }}';">
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('files.show.preview', ['file' => $file->slug]) }}" target="_blank">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <p class="text-sm font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($file->name, 15) }}</p>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <article class="p-[1rem] border border-gray-300 rounded-md shadow-sm bg-gray-50">
                <div class="flex items-start gap-x-5">
                    <div class="w-full">
                        <div class="flex items-center flex-wrap justify-between">
                            <div>
                                <span class="font-semibold">{{ $exchangeMessage->createdBy?->name ?? 'Utilisateur anonyme' }}</span> <span class="text-gray-500 text-sm"> @datetime($exchangeMessage->created_at, capitalized:true)</span>
                            </div>
                        </div>
                        <div class="text-gray-600 mt-1">
                            {{ $exchangeMessage->content }}
                        </div>
                    </div>
                </div>
            </article>
            @php
                $oldDate = $exchangeMessage->created_at;
            @endphp
        </div>
    @endforeach
</div>
