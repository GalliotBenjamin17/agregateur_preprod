<div>
    {{ $this->chooseAction() }}

    <x-filament-actions::modals />

    @script
        <script>
            requestAnimationFrame(() => {
                $wire.mountAction('choose');
            });
        </script>
    @endscript

</div>

