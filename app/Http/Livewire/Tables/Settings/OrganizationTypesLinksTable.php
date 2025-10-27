<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\OrganizationType;
use App\Models\OrganizationTypeLink;
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
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrganizationTypesLinksTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?OrganizationType $organizationType = null;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return OrganizationTypeLink::where('organization_type_id', $this->organizationType->id);
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
                ->modalHeading('Mise à jour du lien')
                ->modalSubmitActionLabel('Mettre à jour'),

            Action::make('Supprimer')
                ->action(function (Model $record, array $data): void {
                    DB::table('user_organization')->where('organization_type_link_id', $record->id)->delete();
                    OrganizationTypeLink::where('organization_type_id', $record->id)->delete();
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
                ->searchable(),
        ];
    }

    public function render()
    {
        return view('livewire.tables.settings.organization-types-table');
    }
}
