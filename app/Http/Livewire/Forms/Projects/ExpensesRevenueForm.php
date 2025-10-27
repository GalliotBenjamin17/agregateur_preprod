<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Models\Project;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;

class ExpensesRevenueForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Project $project;

    public function mount()
    {
        $this->form->fill([
            'expenses' => $this->project->expenses,
            'revenues' => $this->project->revenues,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([

                TextInput::make('sum_expenses')
                    ->disabled()
                    ->suffix('€ HT')
                    ->formatStateUsing(function (Get $get) {
                        $expenses = $get('expenses') ?? [];

                        return collect($expenses)->sum('amount_ht');
                    })
                    ->label('Total dépenses'),

                TextInput::make('sum_revenues')
                    ->disabled()
                    ->suffix('€ HT')
                    ->formatStateUsing(function (Get $get) {
                        $expenses = $get('revenues') ?? [];

                        return collect($expenses)->sum('amount_ht');
                    })
                    ->label('Total recettes'),

                Repeater::make('expenses')
                    ->label('Dépenses')
                    ->addActionLabel('Ajouter une dépense')
                    ->columns(3)
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->collapsible()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $expenses = $get('expenses') ?? [];
                        $total = collect($expenses)->sum('amount_ht');
                        $set('sum_expenses', $total);
                    })
                    ->schema([

                        TextInput::make('label')
                            ->required()
                            ->label('Details'),

                        TextInput::make('amount_ht')
                            ->required()
                            ->numeric()
                            ->suffix('€ HT')
                            ->label('Montant'),

                        ColorPicker::make('color')
                            ->label("Couleur")
                            ->required()
                    ]),

                Repeater::make('revenues')
                    ->label('Recettes')
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->addActionLabel('Ajouter une source de revenu')
                    ->columns(3)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $expenses = $get('revenues') ?? [];
                        $total = collect($expenses)->sum('amount_ht');
                        $set('sum_revenues', $total);
                    })
                    ->schema([

                        TextInput::make('label')
                            ->required()
                            ->label('Details'),

                        TextInput::make('amount_ht')
                            ->required()
                            ->numeric()
                            ->suffix('€ HT')
                            ->label('Montant'),

                        ColorPicker::make('color')
                            ->label("Couleur")
                            ->required()

                    ]),

            ])
            ->statePath('data')
            ->model($this->project);
    }

    public function submit()
    {
        $data = $this->form->getState();

        $this->project->update([
            'expenses' => $data['expenses'],
            'revenues' => $data['revenues'],
        ]);

        defaultSuccessNotification(title: 'Contenu mis à jour.');
    }

    public function render()
    {
        return view('livewire.forms.projects.expenses-revenue-form');
    }
}
