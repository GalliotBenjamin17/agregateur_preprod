@props([
    'url',
    'title',
    'description',
    'isActive' => false
])

<a href="{{ $url }}" @class([
        'flex items-start space-x-2 rounded-lg p-2 transition duration-150 ease-in-out',
        'hover:bg-gray-50' => !$isActive,
        'bg-gray-100' => $isActive
]) {{ $attributes->except('class') }}>
    @isset($icon)
        <div class="flex-shrink-0">
            {{ $icon }}
        </div>
    @endisset
    <div class="leading-none">
        <p class="text-base font-semibold text-gray-900">{{ $title }}</p>
        <p class="text-sm text-gray-500">{{ $description }}</p>
    </div>
</a>
