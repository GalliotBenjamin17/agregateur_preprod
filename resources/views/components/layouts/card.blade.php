@props([
    'groupName' => null,
    'name',
    'withoutHeaderPaddingBottom' => false,
    'thinPadding' => true,
    'backLink' => null,
    'collapsible' => false,
    'collapsed' => false
])

<div @if($collapsible) x-cloak x-data="{collapsed:@js($collapsed)}" @endif {{ $attributes->class(['bg-white rounded-md shadow-sm border border-gray-300' ]) }}>
    <div @if($collapsible) x-on:click="collapsed = !collapsed" @endif class="bg-white flex flex-wrap items-center justify-between gap-y-2 sm:gap-0 px-3 sm:px-4 rounded-t-md rounded-b-md @if($collapsible) no-select cursor-pointer hover:bg-gray-50 hover:rounded-b @endif @if($withoutHeaderPaddingBottom) pt-4 @elseif($thinPadding) py-2 sm:py-3 @else py-4 @endif">
        <div class="flex-shrink-0 flex items-center space-x-3 truncate" >
            @isset($icon)
                {{ $icon }}
            @endisset
            <div class="leading-tight truncate">
                @if($groupName)
                    <div class="flex items-center space-x-2">
                        @if($backLink)
                            <a class="text-[13px] link" href="{{ $backLink }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 inline-flex">
                                    <path fill-rule="evenodd" d="M15 10a.75.75 0 01-.75.75H7.612l2.158 1.96a.75.75 0 11-1.04 1.08l-3.5-3.25a.75.75 0 010-1.08l3.5-3.25a.75.75 0 111.04 1.08L7.612 9.25h6.638A.75.75 0 0115 10z" clip-rule="evenodd" />
                                </svg>
                                <span>Back</span>
                            </a>
                        @endif
                        <h3 class="text-[12px]">{{ $groupName }}</h3>
                    </div>
                @endif
                <h2 class="font-bold text-slate-800 text-[16px]">{{ $name }}</h2>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @isset($actions)
                <div class="flex items-center space-x-2">
                    {{ $actions }}
                </div>
            @endisset
            @if($collapsible)
                <x-icon.big-chevron-top x-show="!collapsed" class="h-5 w-5 cursor-pointer text-gray-500" />
                <x-icon.big-chevron-bottom x-show="collapsed" class="h-5 w-5 cursor-pointer text-gray-500" />
            @endif
        </div>
    </div>
    <div @if($collapsible) x-show="!collapsed" @endif class="bg-white rounded-b-md @if(!$slot->isEmpty() or isset($content)) border-t border-neutral-200 @endif">
        {{ $slot }}
        @isset($content)
            {{ $content }}
        @endisset
    </div>
</div>
