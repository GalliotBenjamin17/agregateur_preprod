@props([
    'title',
    'content'
])

<p {{ $attributes->class(['grid grid-cols-12 gap-x-1 text-[14px]']) }}>
    <span title="{{ $title }}" class="col-span-4 text-gray-600 truncate">{{ $title }}</span>
    <span class="col-span-8 text-gray-700">{{ $content }}</span>
</p>
