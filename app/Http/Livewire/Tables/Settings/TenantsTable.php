<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\Tenant;
use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class TenantsTable extends Component implements HasForms, HasTable
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
        return Tenant::with([
            'createdBy',
        ]);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('delete')
                ->label('Supprimer')
                ->modalDescription("Cette action est irréversible et supprimera tous les informations liées à l'instance locale.")
                ->requiresConfirmation()
                ->color('danger')
                ->visible(function (Tenant $record) {
                    return $record->projects()->count() == 0;
                })
                ->action(function (Tenant $record, array $data): void {
                    $record->delete();
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Tenant $record): string => route('settings.show.tenants', ['tenant' => $record]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->sortable()
                ->description(function (Model $record): string {
                    return $record->domain.'.'.config('app.displayed_url');
                })
                ->searchable(),
            TextColumn::make('domain')
                ->label('Sous-domaine')
                ->searchable()
                ->sortable(),
            TextColumn::make('createdBy.name')
                ->label('Création')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('\A H:i \l\e d/m/Y');
                })
                ->sortable(['created_at']),
        ];
    }

    public function render()
    {
        return view('livewire.tables.settings.tenants-table');
    }
}
