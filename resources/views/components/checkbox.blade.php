@props([
    'content',
    'name',
    'checked' => false,
    'disabled' => false,
])

<div x-cloak {{ $attributes->merge(['class' => 'py-2 mb-0 flex items-center justify-between cursor-pointer']) }} x-data="{ on: {{ $checked ? 'true' : 'false' }} }">
    <span class="truncate text-[.93rem] w-full space-x-1" title="{{ strip_tags($content) }}" x-on:click="on = !on">
        {!! $content !!}
    </span>

    <input type="hidden" name="{{ $name }}" x-model="on">
    <button
        {{ $attributes->only('onclick') }}
        type="button"
        role="switch"
        x-on:click="on = !on"
        @if($disabled) disabled @endif
        x-state:on="Enabled"
        x-state:off="Not Enabled"
        x-bind:class="{ 'bg-emerald-600': on, 'bg-gray-200': !(on) }"
        class="relative inline-flex items-center p-0 flex-shrink-0 h-5 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200"
    >
        <span
            x-state:on="Enabled"
            x-state:off="Not Enabled"
            x-bind:class="{ 'translate-x-6': on, 'translate-x-0': !(on) }"
            class="pointer-events-none relative inline-block h-4 w-4 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 translate-x-6"
        >
            <span
                x-state:on="Enabled"
                x-state:off="Not Enabled"
                x-bind:class="{ 'opacity-0 ease-out duration-100': on, 'opacity-100 ease-in duration-200': !(on) }"
                class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-0 ease-out duration-100"
            >
                <svg class="h-3 w-3 text-rose-400" fill="none" viewBox="0 0 12 12">
                    <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </span>
            <span
                x-state:on="Enabled"
                x-state:off="Not Enabled"
                x-bind:class="{ 'opacity-100 ease-in duration-200': on, 'opacity-0 ease-out duration-100': !(on) }"
                class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-100 ease-in duration-200"
            >
                <svg class="h-3 w-3 text-emerald-600" fill="currentColor" viewBox="0 0 12 12">
                    <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"></path>
                </svg>
            </span>
        </span>
    </button>
</div>
