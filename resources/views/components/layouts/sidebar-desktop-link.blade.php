@props([
    'title',
    'link',
    'active' => false
])

<a href="{{ $link }}" @class([
        'group flex items-center px-2 py-2 text-sm rounded-md',
        'bg-gray-800 text-white font-semibold' => $active,
        'text-gray-50 font-medium hover:text-white hover:bg-gray-800' => !$active
])>
    @isset($icon)
        {{ $icon }}
    @endisset
    <span>{{ $title }}</span>
</a>
