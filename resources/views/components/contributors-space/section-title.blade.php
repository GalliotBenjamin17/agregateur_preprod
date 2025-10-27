@props([
    'title',
    'size' => 'lg', // lg, md, sm
])

<section {{ $attributes->class(['pb-6 flex items-center justify-between']) }}>
    <div class="flex items-center justify-start space-x-3">

        @isset($icon)
            {{ $icon }}
        @endisset


        <h1 @class([
                "leading-tight",
                 "text-3xl" => $size == "lg",
                 "text-2xl" => $size == "md",
                 "text-xl" => $size == "sm",
            ])>{{ $title }}</h1>
    </div>
</section>
