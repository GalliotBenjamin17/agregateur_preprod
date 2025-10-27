<?php

namespace App\Http\Livewire\Tables\Organizations;

use Closure;
use App\Enums\Roles;
use Livewire\Component;
use App\Models\Organization;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class IndexTable extends Component implements HasForms, HasTable
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
        return Organization::with([
            'organizationType',
            'createdBy',
            'tenant',
        ])->withCount([
            'users',
        ])
            ->when(request()->user()->hasRole(Roles::Member), function ($query) {
                if (request()->user()->hasAnyRole([Roles::Admin, Roles::LocalAdmin])) {
                    return $query;
                }

                return $query->whereIn('id', request()->user()->organizations->pluck('id'));
            })
            ->withSum(['donationSplits' => fn ($query) => $query->whereNull('donation_split_id')], 'tonne_co2')
            ->withSum('donations', 'amount');
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->hidden(request()->user()->hasRole(Roles::Member))
                ->relationship('organizationType', 'name'),
            SelectFilter::make('tenant')
                ->hidden(request()->user()->hasRole(Roles::Member))
                ->relationship('tenant', 'name'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Model $record): string => route('organizations.show.details', ['organization' => $record->slug]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->sortable()
                ->description(function (Model $record): string {
                    return $record->address ? \Str::limit($record->address, 70, '...') : 'Aucune adresse renseignée';
                })
                ->searchable(),
            TextColumn::make('organizationType.name')
                ->label("Type d'entité")
                ->sortable(),
            /*TextColumn::make('tenant.name')
                ->label('Antenne locale')
                ->default('-')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->whereRelation('tenant', 'name', 'LIKE', "%{$search}%");
                }),*/
            /*TextColumn::make('contacts.0.name')
                ->default('Aucun contact référencé')
                ->label('Nom du contact')
                ->description(function (Model $record): string {
                    return $record->contacts[0]['phone'] ?? 'Aucun téléphone';
                }),*/
            TextColumn::make('users_count')
                ->label("Nombre d'utilisateurs")
                ->counts('users')
                ->sortable(),

            TextColumn::make('donations_sum_amount')
                ->label('Total contributions')
                ->formatStateUsing(function (Model $record) {
                    if ($record->donations_sum_amount) {
                        return format($record->donations_sum_amount, 2).' €';
                    }

                    return '-';
                })
                ->sortable(),

            TextColumn::make('donation_splits_sum_tonne_co2')
                ->label('Total fléché (tCO2)')
                ->formatStateUsing(function (Model $record) {
                    if ($record->donation_splits_sum_tonne_co2) {
                        return format($record->donation_splits_sum_tonne_co2, 2);
                    }

                    return '-';
                })
                ->sortable(),

            TextColumn::make('createdBy.name')
                ->label('Création')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('\A H:i \l\e d/m/Y');
                })
                ->sortable(['created_at']),
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
        return view('livewire.tables.organizations.index-table');
    }
}
