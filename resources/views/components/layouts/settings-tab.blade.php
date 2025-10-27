@props([
    'active',
    'href',
    'title',
    'rounded' => true
])

<a href="{{ $href }}" @class([
    'group flex items-center px-3 py-2 text-sm font-medium',
    'rounded-md' => $rounded,
    'border-l-4 border-[#4194f9] bg-[#4194f91a]' => !$rounded and $active,
    'bg-gray-100 text-gray-900 group flex items-center px-3 py-2 text-sm font-medium' => $active,
    'text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-medium' => !$active
]) aria-current="page">
    @isset($icon)
        {{ $icon }}
    @endisset
    <span class="truncate">{{ $title }}</span>
</a>
