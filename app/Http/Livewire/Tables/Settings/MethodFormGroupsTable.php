<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\MethodFormGroup;
use App\Models\Segmentation;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class MethodFormGroupsTable extends Component implements HasActions, HasForms, HasTable
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
        return MethodFormGroup::without([
            'methodForms',
        ])->with([
            'activeMethodForm',
        ]);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Modifier')
                ->mountUsing(function (ComponentContainer $form, Model $record) {
                    $form->fill([
                        'name' => $record->name,
                        'description' => $record->description,
                        'active_method_form_id' => $record->active_method_form_id,
                        'segmentation_id' => $record->segmentation_id,
                    ]);
                })
                ->action(function (Model $record, array $data): void {
                    $record->update([
                        'name' => $data['name'],
                        'description' => $data['description'],
                        'active_method_form_id' => $data['active_method_form_id'],
                        'segmentation_id' => $data['segmentation_id'],
                    ]);
                })
                ->form([
                    TextInput::make('name')
                        ->label('Nom de la méthode')
                        ->lazy()
                        ->required()
                        ->columnSpanFull()
                        ->autofocus(),

                    RichEditor::make('description')
                        ->columnSpanFull()
                        ->label('Description'),

                    Select::make('active_method_form_id')
                        ->label('Version active')
                        ->searchable()
                        ->options(function (MethodFormGroup $record) {
                            return $record->methodForms()->whereNotNull('locked_at')->get()->pluck('name', 'id')->toArray();
                        }),

                    Select::make('segmentation_id')
                        ->label('Segmentation')
                        ->searchable()
                        ->options(Segmentation::pluck('name', 'id')->toArray()),

                ])
                ->modalHeading('Mise à jour de la méthode')
                ->modalSubmitActionLabel('Mettre à jour'),

        ];
    }

    protected function getTableRecordUrlUsing(): \Closure
    {
        return fn (Model $record): string => route('settings.method-form-groups.show', ['methodFormGroup' => $record]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->searchable()
                ->description(function (Model $record) {
                    return $record->description ? Str::limit(strip_tags($record->description), 50) : 'Aucune description';
                })
                ->sortable(),

            TextColumn::make('activeMethodForm.name')
                ->default('-')
                ->label('Version active'),

            TextColumn::make('method_forms_count')
                ->label('Nombre de versions')
                ->counts('methodForms'),

            TextColumn::make('segmentation.name')
                ->label('Segmentation'),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'updated_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function render()
    {
        return view('livewire.tables.settings.method-form-groups-table');
    }
}
