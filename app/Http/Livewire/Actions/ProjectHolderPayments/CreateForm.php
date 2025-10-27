<?php

namespace App\Http\Livewire\Actions\ProjectHolderPayments;

use App\Helpers\TVAHelper;
use App\Models\Project;
use App\Models\ProjectHolderPayment;
use App\Services\Models\ProjectHolderPaymentService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Livewire\Component;

class CreateForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Project $project;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajouter un paiement')
            ->size(ActionSize::ExtraSmall)
            ->modalDescription('Vous pouvez créer un paiement à destination du porteur de projet.')
            ->mountUsing(function (Form $form) {
                $form->fill([
                    'created_at' => now(),
                ]);
            })
            ->form([
                Grid::make()
                    ->schema([

                        TextInput::make('amount_ht')
                            ->label('Montant (€ HT)')
                            ->suffix(' € HT')
                            ->numeric()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('amount', TVAHelper::getTTC($state));
                            })
                            ->live(onBlur: true)
                            ->required(),

                        TextInput::make('amount')
                            ->label('Montant (€ TTC)')
                            ->suffix(' € TTC')
                            ->required(),

                        DatePicker::make('created_at')
                            ->label('Paiement effectué le')
                            ->required(),

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
                $paymentService = new ProjectHolderPaymentService();
                $paymentService->store([
                    ...$data,
                    'receipt' => \Arr::get($data, 'receipt') ? '/storage/'.$data['receipt'] : null,
                    'project_id' => $this->project->id,
                    'created_by' => request()->user()->id,
                ]);

                $this->dispatch('projectHolderPaymentAdded');

                Notification::make()
                    ->title('Paiement au porteur ajoutée')
                    ->success()
                    ->send();
            })
            ->model(ProjectHolderPayment::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.project-holder-payments.create-form');
    }
}
