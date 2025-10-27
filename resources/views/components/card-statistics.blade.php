@props([
    'title',
    'subTitle' => null,
    'number'
])

<div {{ $attributes->merge(['class' => "relative bg-white dark:bg-night-dark rounded-md border shadow-sm py-2 px-3 flex items-center space-x-2 hover:text-gray-900 col-span-1"]) }}>
    @isset($icon)
        <div class="flex-shrink-0">
            {{ $icon }}
        </div>
    @endisset
    <div class="">
        <h6 class="uppercase text-xs font-medium text-blue-gray-500 dark:text-white">
            {{ $title }}
        </h6>
        <h2 class="text-xl font-bold dark:text-night-white">
            {{ $number }}
            @if($subTitle)
                <span class="font-normal text-xs">{{ $subTitle }}</span>
            @endif
        </h2>
    </div>
</div>
