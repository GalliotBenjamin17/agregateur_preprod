<?php

namespace App\Http\Livewire\Tables\Users;

use App\Enums\Roles;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Illuminate\Contracts\Pagination\Paginator;

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
        return User::with([
            'tenant',
            'organizations',
            'roles',
        ])
            ->withSum(['donationSplits' => fn ($query) => $query->whereNull('donation_split_id')], 'tonne_co2')
            ->withSum('donations', 'amount')
            ->withCount([
                'projectsSponsor',
                'projectsAuditor',
                'projectsReferent',
            ])->when(userHasTenant(), function ($query) {
            return $query->where('tenant_id', userTenantId());
        })
            ->role([
                Roles::Sponsor,
                Roles::Referent,
                Roles::Auditor,
                Roles::Contributor,
                Roles::Subscriber,
                Roles::Member,
                Roles::Partner,
            ]);
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('tenant_filter')
                ->multiple()
                ->visible(request()->user()->hasAnyRole(['admin']))
                ->label('Antenne locale')
                ->relationship('tenant', 'name'),

            SelectFilter::make('organization_filter')
                ->multiple()
                ->label('Organisation')
                ->relationship('organizations', 'name'),

            Filter::make('created_at')
                ->form([
                    Forms\Components\Select::make('role')
                        ->label('Rôle')
                        ->searchable()
                        ->options([
                            Roles::Sponsor     => 'Porteurs',
                            Roles::Referent    => 'Référents',
                            Roles::Auditor     => 'Auditeurs',
                            Roles::Contributor => 'Contributeurs',
                            Roles::Subscriber  => 'Abonnés',
                        ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['role'],
                            fn($query, $roles) => $query->whereRelation('roles', 'name', '=', $data['role']),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['role'] ?? null) {
                        $indicators['role'] = \Arr::get(Roles::toDisplay(), $data['role']);
                    }

                    return $indicators;
                }),

        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('send_link')
                ->label('Email de connexion')
                ->icon('heroicon-c-envelope-open')
                ->action(function (User $record) {
                    $record->sendWelcomeNotification(now()->addMonth());

                    Notification::make()
                        ->success()
                        ->title('Email envoyé')
                        ->send();
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn(Model $record): string => route('users.show.details', ['user' => $record->slug]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->description(function (User $record) {
                    return $record->roles
                        ->pluck('name')
                        ->map(fn($value) => \Arr::get(Roles::toDisplay(), $value))
                        ->join(', ', ' et ');
                })
                ->sortable(['first_name'])
                ->searchable(['first_name', 'last_name']),

          TextColumn::make('organizations.name')
                ->label('Organisation')
                ->default('-')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->whereRelation('organizations', 'name', 'LIKE', "%{$search}%");
                })
                ->limit(50)
                ->description(function (User $record) {
                    return $record->organization?->pivot?->organization_type_link_id;
                }),

            TextColumn::make('projects_count')
                ->label('Projets impliqués')
                ->formatStateUsing(function (User $record) {
                    return $record->projects_sponsor_count + $record->projects_auditor_count + $record->projects_referent_count;
                })
                ->counts('projects'),

            TextColumn::make('donations_sum_amount')
                ->label('Total contributions')
                ->formatStateUsing(function (Model $record) {
                    if ($record->donations_sum_amount) {
                        return format($record->donations_sum_amount, 2) . ' €';
                    }

                    return '-';
                })
                ->default('-')
                ->sum('donations', 'amount')
                ->sortable(),

            TextColumn::make('donation_splits_sum_tonne_co2')
                ->label('Total fléché (tCO2)')
                ->formatStateUsing(function (Model $record) {
                    if ($record->donation_splits_sum_tonne_co2) {
                        return format($record->donation_splits_sum_tonne_co2, 2);
                    }

                    return '-';
                })
                ->default('-')
                //->sum('donationSplits', 'tonne_co2')
                ->sortable(),

            TextColumn::make('created_at')
                ->label("Date d'inscription")
                ->date('d/m/Y')
                ->sortable(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [15, 25, 50, 100];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate($this->getTableRecordsPerPage());
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
        return view('livewire.tables.users.index-table');
    }
}
