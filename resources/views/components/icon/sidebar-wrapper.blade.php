@props([
	'active' => false,
])

<div @class(['@w-6', '@text-slate-400 group-hover:@text-slate-600' => !$active, '@text-slate-800' => $active])>
    {{ $slot }}
</div>
