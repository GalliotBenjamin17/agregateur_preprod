@props([
	'id' => Str::orderedUuid(),
	'size' => '',
	'scrollable' => false,
	'centered' => false,
])

<div class="modal fade" id="{{ $id }}" >
    <div @class([
        'modal-dialog',
        'modal-xl' => $size == 'xl',
        'modal-lg' => $size == 'lg',
        'modal-sm' => $size == 'sm',
        'modal-dialog-scrollable' => $scrollable,
        'modal-dialog-centered' => $centered,
    ]) role="document">
        <div class="modal-content !bg-white !rounded-md">
            {{ $slot }}
        </div>
    </div>
</div>
