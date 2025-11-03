<div {{ $attributes->class(['mt-1 sm:tra md:pb-0 border-b-2 border-gray-300 flex items-center overflow-visible']) }}>
    <div class="flex">
        @isset($slot)
            {{ $slot }}
        @endisset
    </div>
</div>