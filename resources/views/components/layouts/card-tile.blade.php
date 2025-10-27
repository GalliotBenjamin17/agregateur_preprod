@props([
    'link' => null,
    'title'
])

<div {{ $attributes }}>
    <a href="{{ $link ?? "#!" }}"
       class="flex items-center justify-between hover:text-opacity-80 text-[#0176D3]">
        <h6 title="{{ $title }}" class="text-[15px] truncate underline decoration-dotted underline-offset-4">
            {{ $title }}
        </h6>
        <span class="hidden md:block">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-auto">
                <path fill-rule="evenodd" d="M2 10a.75.75 0 01.75-.75h12.59l-2.1-1.95a.75.75 0 111.02-1.1l3.5 3.25a.75.75 0 010 1.1l-3.5 3.25a.75.75 0 11-1.02-1.1l2.1-1.95H2.75A.75.75 0 012 10z" clip-rule="evenodd"/>
            </svg>
        </span>
    </a>
    @isset($tiles)
        <div class="mt-2">
            {{ $tiles }}
        </div>
    @endisset
</div>
