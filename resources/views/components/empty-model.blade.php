@props([
    'model',
    'content',
    'height' => '128',
])

<div  {{ $attributes->merge(['class' => "h-full flex flex-col place-content-center justify-center items-center mx-auto py-2"]) }}>
    <div class="grayscale opacity-20">
        {!! \App\Helpers\IconHelper::viewIcon(model: $model) !!}
    </div>
    <p class="mt-2 text-gray-500 text-center text-sm">
        {{ new \Illuminate\Support\HtmlString($content) }}
    </p>
    {{ $slot }}
</div>
