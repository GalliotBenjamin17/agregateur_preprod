<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\Terminal;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class TerminalsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return Terminal::with([
            'tenant',
        ]);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Modifier')
                ->mountUsing(function (ComponentContainer $form, Terminal $record) {
                    $form->fill([
                        'id' => $record->id,
                        'name' => $record->name,
                    ]);
                })
                ->action(function (Terminal $record, array $data): void {
                    $record->id = $data['id'];
                    $record->name = $data['name'];
                    $record->save();
                })
                ->form([
                    Fieldset::make('name')
                        ->label('Dénomination')
                        ->schema([

                            TextInput::make('id')
                                ->required()
                                ->label('Id'),

                            TextInput::make('name')
                                ->label('Nom de la borne')
                                ->lazy()
                                ->required()
                                ->autofocus(),
                        ]),
                ])
                ->modalHeading('Mise à jour de la borne')
                ->modalSubmitActionLabel('Mettre à jour'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->sortable()
                ->searchable(),

            TextColumn::make('id')
                ->label('ID')
                ->weight('semibold')
                ->sortable()
                ->searchable(),

            TextColumn::make('tenant.name')
                ->label('Instance locale'),
        ];
    }

    public function render()
    {
        return view('livewire.tables.settings.terminals-table');
    }
}
