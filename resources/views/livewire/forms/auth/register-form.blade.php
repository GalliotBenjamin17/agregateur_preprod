<div>
    <form wire:submit="submit">
        {{ $this->form }}

        <x-honeypot livewire-model="extraFields" />

        <div class="pt-2 mt-3 flex">
            <x-button type="success" class="w-full !bg-[#244999]"  size="lg" submit>
                Créer un compte
            </x-button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
