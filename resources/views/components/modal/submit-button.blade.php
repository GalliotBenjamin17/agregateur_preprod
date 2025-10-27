@props([
	'class' => false
])

<button type="submit" @class(['btn btn-info', $class]) {{ $attributes }}>
    {{ $slot }}
</button>
