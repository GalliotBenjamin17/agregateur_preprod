@props([
    'href',
    'title'
])

<a href="{{ $href }}" {{ $attributes->class(['flex items-center space-x-3 bg-gray-50 hover:bg-gray-100 p-3 rounded-lg']) }}>
    @isset($icon)
        {{ $icon }}
    @endisset
    <div>
        {{ $title }}
    </div>
</a>
