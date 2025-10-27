<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\Project;
use App\Models\Segmentation;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class SegmentationsTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
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
        return Segmentation::withCount([
            'projects',
            'methodFormGroups',
        ]);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Modifier')
                ->mountUsing(function (ComponentContainer $form, Segmentation $segmentation) {
                    $form->fill([
                        'segmentation' => $segmentation->toArray(),
                    ]);
                })
                ->action(function (Model $record, array $data): void {
                    $record->update($data['segmentation']);

                    defaultSuccessNotification('Informations mises à jour.');
                })
                ->form([
                    Grid::make('2')->schema(components: [
                        TextInput::make('segmentation.name')
                            ->label('Nom')
                            ->columnSpanFull()
                            ->required(),

                        Textarea::make('segmentation.description')
                            ->columnSpanFull()
                            ->rows(3)
                            ->label('Description'),

                        Fieldset::make('Graphiques')
                            ->schema([

                                ColorPicker::make('segmentation.chart_color')
                                    ->required()
                                    ->label('Couleur dans le graphique'),

                                TextInput::make('segmentation.chart_spread_years')
                                    ->label("Nombre d'années d'étalement")
                                    ->numeric()
                                    ->integer()
                                    ->required(),
                            ]),

                    ]),
                ])
                ->slideOver()
                ->size('sm')
                ->modalHeading('Mise à jour de la segmentation')
                ->modalSubmitActionLabel('Mettre à jour'),

            Action::make('Supprimer')
                ->action(function (Model $record, array $data): void {
                    Project::where([
                        'segmentation_id' => $record->id,
                    ])->update([
                        'segmentation_id' => null,
                    ]);
                    $record->delete();
                })
                ->requiresConfirmation()
                ->color('danger'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->sortable()
                ->description(function (Model $record) {
                    return \Str::limit($record->description, 70);
                })
                ->searchable(),

            TextColumn::make('projects_count')
                ->label('Nombre de projets')
                ->counts('projects'),

            TextColumn::make('method_form_groups_count')
                ->label('Nombre de méthodes')
                ->counts('methodFormGroups'),
        ];
    }

    public function render()
    {
        return view('livewire.tables.settings.segmentations-table');
    }
}
