<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\OrganizationTypeLink;
use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
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

class OrganizationTypesTable extends Component implements HasForms, HasTable
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
        return OrganizationType::withCount([
            'organizations',
            'organizationTypeLinks',
        ]);
    }

    protected function getTableFilters(): array
    {
        return [
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Modifier')
                ->mountUsing(function (ComponentContainer $form, Model $record) {
                    $form->fill([
                        'name' => $record->name,
                    ]);
                })
                ->action(function (Model $record, array $data): void {
                    $record->update([
                        'name' => $data['name'],
                    ]);
                })
                ->form([
                    Grid::make('2')->schema(components: [
                        TextInput::make('name')
                            ->label('Nom')
                            ->columnSpanFull()
                            ->required(),
                    ]),
                ])
                ->modalHeading("Mise à jour du type d'entité")
                ->modalSubmitActionLabel('Mettre à jour'),

            Action::make('Supprimer')
                ->action(function (Model $record, array $data): void {
                    Organization::where('organization_type_id', $record->id)->update([
                        'organization_type_id' => null,
                    ]);
                    OrganizationTypeLink::where('organization_type_id', $record->id)->delete();
                    $record->delete();
                })
                ->requiresConfirmation()
                ->color('danger'),
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Model $record): string => route('organization-types.show', ['organizationType' => $record->slug]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->sortable()
                ->searchable(),
            TextColumn::make('organizations_count')
                ->label("Nombre d'organisations")
                ->counts('organizations')
                ->sortable(),
            TextColumn::make('organization_type_links_count')
                ->label('Nombre de liens existants')
                ->counts('organizationTypeLinks')
                ->sortable(),

        ];
    }

    public function render()
    {
        return view('livewire.tables.settings.organization-types-table');
    }
}
