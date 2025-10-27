@props([
    'groupName',
    'name',
    'withoutHeaderPaddingBottom' => false
])

<div {{ $attributes->class(['bg-white rounded-md shadow-sm border border-gray-300 overflow-hidden']) }}>
    <div class="bg-gray-light flex flex-wrap items-center justify-between px-5 @if(isset($content) && $withoutHeaderPaddingBottom) pt-4 @else py-4 @endif">
        <div class="flex-shrink-0 flex items-center space-x-3">
            @isset($icon)
                {{ $icon }}
            @endisset
            <div class="leading-tight">
                <h3 class="text-[13px]">{{ $groupName }}</h3>
                <h2 class="font-bold text-[18px]">{{ $name }}</h2>
            </div>
        </div>
        <div class=" mt-3 md:mt-0">
            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    </div>
    <div class="bg-white">
        @isset($content)
            {{ $content }}
        @endisset
    </div>
</div>
