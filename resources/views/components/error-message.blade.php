@props([
    'errors',
    'key'
])

@if($errors->has($key))
    <div class="mt-1 text-sm text-red-500">
        {{ $errors->first($key) }}
    </div>
@endif
