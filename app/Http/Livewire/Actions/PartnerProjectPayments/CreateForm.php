<?php

namespace App\Http\Livewire\Actions\PartnerProjectPayments;

use App\Enums\Models\PartnerProjectPayments\PaymentStateEnum;
use App\Models\PartnerProject;
use App\Models\PartnerProjectPayment;
use App\Services\Models\PartnerProjectPaymentService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Livewire\Component;

class CreateForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public PartnerProject $partnerProject;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajouter un paiement')
            ->size(ActionSize::Small)
            ->modalDescription('Vous pouvez créer un paiement à destination du partenaire.')
            ->mountUsing(function (Form $form) {
                $form->fill([
                    'created_at' => now(),
                ]);
            })
            ->form([
                Grid::make()
                    ->schema([
                        Select::make('payment_state')
                            ->searchable()
                            ->label('Statut')
                            ->reactive()
                            ->required()
                            ->options(PaymentStateEnum::toArray()),

                        DateTimePicker::make('scheduled_at')
                            ->label('Planifié à')
                            ->native()
                            ->seconds(false)
                            ->required(fn (Get $get) => $get('payment_state') == PaymentStateEnum::Scheduled->databaseKey())
                            ->visible(fn (Get $get) => $get('payment_state') == PaymentStateEnum::Scheduled->databaseKey()),

                        TextInput::make('amount')
                            ->label('Montant')
                            ->suffix(' € TTC')
                            ->required(),

                        DatePicker::make('created_at')
                            ->required(fn (Get $get) => $get('payment_state') == PaymentStateEnum::Sent->databaseKey())
                            ->visible(fn (Get $get) => $get('payment_state') == PaymentStateEnum::Sent->databaseKey())
                            ->label('Effectué le'),

                        FileUpload::make('receipt')
                            ->openable()
                            ->columnSpanFull()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(function ($file): string {
                                return formatFileName($file->getClientOriginalName());
                            })
                            ->label('Justificatif'),
                    ]),
            ])
            ->action(function (array $data) {
                $paymentService = new PartnerProjectPaymentService();
                $paymentService->store([
                    ...$data,
                    'receipt' => \Arr::get($data, 'receipt') ? '/storage/'.$data['receipt'] : null,
                    'partner_project_id' => $this->partnerProject->id,
                ]);

                $this->dispatch('partnerProjectPaymentAdded');

                Notification::make()
                    ->title('Commission intermédiaire ajoutée')
                    ->body('Vous pouvez la modifier dans la table sur cette même page')
                    ->success()
                    ->send();
            })
            ->model(PartnerProjectPayment::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.partner-project-payments.create-form');
    }
}
