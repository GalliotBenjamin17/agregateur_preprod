<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">

        <div class="flex items-center space-x-2 rtl:space-x-reverse group">
            <div class="flex-1">
                <input
                    type="text"
                    wire:ignore
                    x-model="state"
                    class="fi-input block w-full text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 ps-3 pe-3 rounded-md w-full"
                    required
                    id="{{ $getId() }}"
                    x-bind:class="{
                    'border-gray-300': ! (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
                    'dark:border-gray-600': ! (@js($getStatePath()) in $wire.__instance.serverMemo.errors) && @js(config('forms.dark_mode')),
                    'border-danger-600 ring-danger-600': (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
                    'dark:border-danger-400 dark:ring-danger-400': (@js($getStatePath()) in $wire.__instance.serverMemo.errors) && @js(config('forms.dark_mode')),
                }"
                />
            </div>
        </div>

        <span class="isolate flex items-center justify-between rounded-md shadow-sm mt-1 w-full">
            <button type="button" x-on:click="state = {{ $getMaxAmount() }} * 0.35" wire:click="dispatchFormEvent('updateHelperText')" class="relative w-full justify-center inline-flex items-center rounded-l-md bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                35 %
            </button>
            <button type="button" x-on:click="state = {{ $getMaxAmount() }} * 0.5" wire:click="dispatchFormEvent('updateHelperText')" class="relative w-full justify-center -ml-px inline-flex items-center bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                50 %
            </button>
            <button type="button" x-on:click="state = {{ $getMaxAmount() }}  * 0.75" wire:click="dispatchFormEvent('updateHelperText')" class="relative w-full justify-center -ml-px inline-flex items-center bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                75%
            </button>
            <button type="button" x-on:click="state = {{ $getMaxAmount() }}" wire:click="dispatchFormEvent('updateHelperText')" class="relative w-full justify-center -ml-px inline-flex items-center rounded-r-md bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                100 %
            </button>
        </span>

    </div>
</x-dynamic-component>
