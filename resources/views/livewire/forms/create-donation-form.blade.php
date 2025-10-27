<div>
    @if(!$transaction)
        <form wire:submit="submit">
            {{ $this->form }}

            <div class="pt-2 mt-3 flex justify-end">
                <x-button submit size="lg" type="success">
                    Envoyer
                </x-button>
            </div>
        </form>
    @else
        <x-information-alert
            type="success"
            title="Lien de paiement créé."
            message=" Vous pouvez transmettre le lien de paiement à l'organisation : <a class='hover:underline' href={{ $transaction->payment_url }}>{{ $transaction->payment_url }}</a>"
        />
    @endif
</div>
