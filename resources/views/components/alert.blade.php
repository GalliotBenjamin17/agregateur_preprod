@props([
	'type' => 'error',
	'delay' => 5000,
	'dismissible' => true,
	'push' => false,
])

<div
    x-data="{
        visible: @js(!$push),
        dismissible: @js($dismissible),
        push: @js($push),
        close() {
            this.visible = false;
            setTimeout(() => $refs.alert.remove(), 500);
        },
        init() {
            if(this.push) {
                document.getElementById('alerts-bag').append($el);
                this.visible = true;
            }
            if(!this.dismissible) {
                setTimeout(this.close, {{ $delay }});
            }
        },
    }"

    x-ref="alert"
    x-show="visible"

    @if($push)
    x-cloak
    @endif

    @class([
        'relative border z-60 rounded-md shadow p-3 flex gap-3 max-w-[500px]',
        'bg-rose-600 border-rose-800 text-white' => $type === 'error',
        'bg-green-600 border-green-800 text-white' => $type === 'success',
        'bg-gray-50 border-gray-300 text-gray-900' => $type === 'info',
    ])
>
    <div class="shrink-0 p-0.5">

    </div>

    <div class="text-sm pt-1">
        {{ $slot }}
    </div>

    @if($dismissible)
        <div
            @class([
                'ml-auto cursor-pointer shrink-0 rounded-md p-0.5 hover:bg-black/10 self-start transition',
                'text-rose-200 hover:text-white' => $type === 'error',
                'text-green-200 hover:text-white' => $type === 'success',
                'text-gray-600 hover:text-gray-900' => $type === 'info',
            ])
            x-on:click="close()"
        >
            <x-icon.fermer class="h-6 w-6"/>
        </div>
    @endif
</div>
