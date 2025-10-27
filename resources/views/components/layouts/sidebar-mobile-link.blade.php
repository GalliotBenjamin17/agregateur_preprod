@props([
    'title',
    'link',
    'active' => false
])

<a href="{{ $link }}" @class([
        'group flex items-center px-2 py-2 text-base font-medium',
        'bg-gray-100 text-gray-900 rounded-md' => $active,
        'text-gray-800 font-medium' => !$active
])>
    @isset($icon)
        {{ $icon }}
    @endisset
    <span>{{ $title }}</span>
</a>
