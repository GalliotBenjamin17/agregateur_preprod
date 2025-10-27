<div>
    <div @class([
        'fi-ta-top-without-corner' => request()->routeIs('news.index')
    ])>
        {{ $this->table }}
    </div>

    <x-filament-actions::modals />
</div>
