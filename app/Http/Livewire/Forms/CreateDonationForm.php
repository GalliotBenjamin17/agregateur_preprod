<?php

namespace App\Http\Livewire\Forms;

use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Models\TransactionService;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class CreateDonationForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public ?Transaction $transaction = null;

    public array $usersSponsor = [];

    public array $organizationSponsor = [];

    public function mount()
    {
        $this->organizationSponsor = Organization::select(['id', 'name', 'billing_email'])
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->usersSponsor = User::select(['id', 'first_name', 'last_name', 'tenant_id'])
            ->tenantable()
            ->orderBy('first_name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->form->fill([
            'billing_email' => null,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('organization_id')
                        ->searchable()
                        ->label('Organisation')
                        ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                            $set('billing_email', Organization::find($state)?->billing_email);
                        })
                        ->required()
                        ->reactive()
                        ->options($this->organizationSponsor),

                    TextInput::make('amount')
                        ->label('Montant (TTC)')
                        ->required()
                        ->numeric()
                        ->suffix(' €'),

                    TextInput::make('billing_email')
                        ->label('Adresse mail de réception')
                        ->reactive()
                        ->email()
                        ->required(),

                    Toggle::make('send_email')
                        ->columnSpanFull()
                        ->reactive()
                        ->label("Envoyer un email à l'organisation"),

                    RichEditor::make('email_content')
                        ->visible(fn (\Filament\Forms\Get $get) => $get('send_email'))
                        ->disableToolbarButtons(['attachFiles', 'codeBlock', 'h2', 'h3', 'link', 'attachFiles', 'blockquote', 'strike'])
                        ->name("Contenu de l'email")
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function submit()
    {
        $organization = Organization::with('tenant')->findOrFail($this->form->getState()['organization_id']);

        $transactionService = new TransactionService($organization->tenant);

        try {
            $this->transaction = $transactionService->createTransaction(related: $organization, amount: $this->form->getState()['amount']);

            if ($this->form->getState()['send_email']) {
                $transactionService->sendEmail($this->transaction, email: $this->form->getState()['billing_email'], text: $this->form->getState()['email_content']);
            }

            Notification::make()
                ->title('Lien de paiement généré')
                ->body('Le lien de paiement a été créé et est disponible.')
                ->success()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('open')
                        ->label('Ouvrir')
                        ->url(asset($this->transaction->payment_url), shouldOpenInNewTab: true)
                        ->button(),
                ])
                ->send();
        } catch (\TypeError $e) {
            dd($e);
            Notification::make()
                ->title('Erreur lors de la création du lien')
                ->body("Aucun lien de paiement n'a été généré suite à une erreur lors de la connexion avec le prestataire de paiement.")
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.forms.create-donation-form');
    }
}
