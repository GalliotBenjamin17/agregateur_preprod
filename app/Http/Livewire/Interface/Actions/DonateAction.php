<?php

namespace App\Http\Livewire\Interface\Actions;

use App\Models\Project;
use App\Services\Models\TransactionService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Set;
use Filament\Support\Enums\IconPosition;
use Livewire\Component;

class DonateAction extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Project $project;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Contribuer à nouveau')
            ->size('sm')
            ->color('gray')
            ->iconPosition(IconPosition::After)
            ->icon('heroicon-m-cursor-arrow-rays')
            ->form([

                Grid::make(2)
                    ->schema([

                        TextInput::make('amount')
                            ->label('Montant')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('tons', round($state / $this->project->activeCarbonPrice->price, 2));
                            })
                            ->suffix('€'),

                        TextInput::make('tons')
                            ->label('Tonnes')
                            ->disabled()
                            ->suffix('tCO2'),

                    ]),

            ])
            ->action(function (Component $livewire, array $data) {

                $tenant = $this->project->tenant;

                $transactionService = new TransactionService($tenant);
                $transaction = $transactionService->createTransaction(
                    related: auth()->user(),
                    amount: $data['amount'],
                    project: $this->project,
                );

                return redirect()->to($transaction->payment_url);

            });
    }

    public function render()
    {
        return view('livewire.interface.actions.donate-action');
    }
}
