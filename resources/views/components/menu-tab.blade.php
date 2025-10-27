@props([

    'content',
    'url' => null,
    'isActive' => false,
    'withSubmenu' => false
])

@if($withSubmenu)
    <div class="relative" x-data="{ open: false }">
        <a href="#!" x-on:click="open=true;"  @class([
                'px-[0.85rem] group flex items-center space-x-2 py-1 text-[16px] bg-opacity-70 rounded-t-[.25rem]',
                'bg-gray-200 border-t-2 border-gray-700 hover:bg-opacity-100' => $isActive,
                'hover:bg-gray-200' => !$isActive
             ])>
            <span>{{ $content }}</span>
            <x-icon.chevron_bas class="h-5 w-5 text-gray-500 rounded-md hover:bg-gray-50" />
        </a>

        <div x-cloak x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="absolute left-0 z-50 mt-2 w-screen max-w-sm transform px-2 sm:px-0" @click.away="open = false">
            <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="relative grid gap-y-1 bg-white px-5 py-6 sm:p-1">
                    @isset($slot)
                        {{ $slot }}
                    @endisset
                </div>
            </div>
        </div>
    </div>
@else
    <a href="{{ $url }}" wire:navigate.hover {{ $attributes->class([
        'px-[0.85rem] group w-max flex items-center space-x-2 py-1 text-[16px] bg-opacity-70 rounded-t-[.25rem]',
        'bg-gray-200 border-t-2 border-slate-700 hover:bg-opacity-100' => $isActive,
        'hover:bg-gray-200' => !$isActive
     ]) }}>
        <span>{{ $content }}</span>
    </a>
@endif

