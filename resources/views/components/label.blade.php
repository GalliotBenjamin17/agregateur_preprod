@props([
    'value',
    'required' => false
])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500 text-sm">*</span>
    @endif
</label>
