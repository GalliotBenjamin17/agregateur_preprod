<?php

namespace App\Http\Livewire\Tables\Projects;

use App\Enums\Models\Projects\ProjectStateEnum;
use App\Enums\Roles;
use App\Models\Certification;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Models\UserTablePreference;
use Illuminate\Support\Facades\Schema;
use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\BulkAction;
// removed header quick-filter actions
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;

class IndexTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public ?Organization $organization = null;

    public ?Project $project = null;

    protected string $tablePreferenceKey = 'projects.index.table';

    protected array $activeSavedFilters = [];

    public function mount(): void
    {
        $this->loadUserColumnToggles();

        // Reset saved filters explicitly
        if (request()->boolean('resetFilters')) {
            $this->clearSavedFiltersForUser();
            // Redirect to clean URL without params and without the reset flag
            $this->redirect(request()->url(), navigate: true);
            return;
        }

        // Persist current filters automatically when present
        $current = $this->getCurrentFiltersFromRequest();
        if (! empty($current)) {
            $this->saveFiltersArrayForUser($current);
        } else {
            // No filters in URL: set activeSavedFilters from saved values (no redirect)
            $this->activeSavedFilters = $this->getSavedFiltersForUser();
        }
    }

    protected function getTableQuery(): Builder
    {
        $user = request()->user()->load([
            'organizations',
        ]);

        return Project::with([
            'tenant',
            'sponsor',
            'auditors',
            'referent',
            'createdBy',
            'certification',
            'donationSplits.childrenSplits',
        ])
            ->withCount([
                'childrenProjects',
            ])
            ->when($this->organization, function ($query) {
                return $query->where('sponsor_id', $this->organization->id)
                    ->where('sponsor_type', get_class($this->organization));
            })->when($this->project, function ($query) {
                return $query->where('parent_project_id', $this->project->id);
            }, function ($query) {
                return $query->whereNull('parent_project_id');
            })
            ->when($user->hasRole(Roles::Referent), function ($query) use ($user) {
                return $query->where('referent_id', $user->id);
            })
            ->when($user->hasRole(Roles::Auditor), function ($query) use ($user) {
                return $query->whereRelation('auditors', 'id', '=', $user->id);
            })
            ->when($user->hasRole(Roles::Sponsor), function ($query) use ($user) {
                return $query->where('sponsor_id', $user->id);
            })
            ->when($user->hasRole(Roles::Member), function ($query) use ($user) {
                return $query->whereIn('sponsor_id', $user->organizations->pluck('id')->toArray());
            })
            ->when($user->hasRole(Roles::Partner), function ($query) use ($user) {
                return $query->whereHas('projectPartners', function ($q) use ($user) {
                    return $q->whereIn('partner_id', $user->partners()->pluck('id')->toArray());
                });
            })
            ->when($this->getFilterParam('cf_id'), function ($query, $value) {
                $ids = is_array($value) ? $value : (str_contains(strval($value), ',') ? explode(',', strval($value)) : null);
                if ($ids) {
                    return $query->whereIn('certification_id', $ids);
                }
                return $query->where('certification_id', $value);
            })
            ->when($this->getFilterParam('mf_id'), function ($query, $value) {
                $ids = is_array($value) ? $value : (str_contains(strval($value), ',') ? explode(',', strval($value)) : null);
                if ($ids) {
                    return $query->whereIn('method_form_id', $ids);
                }
                return $query->where('method_form_id', $value);
            })
            ->when($this->getFilterParam('sg_id'), function ($query, $value) {
                $ids = is_array($value) ? $value : (str_contains(strval($value), ',') ? explode(',', strval($value)) : null);
                if ($ids) {
                    return $query->whereIn('segmentation_id', $ids);
                }
                return $query->where('segmentation_id', $value);
            })
            ->when($this->getFilterParam('st'), function ($query, $value) {
                $vals = is_array($value) ? $value : (str_contains(strval($value), ',') ? explode(',', strval($value)) : null);
                if ($vals) {
                    return $query->whereIn('state', $vals);
                }
                return $query->where('state', $value);
            });
    }



    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Model $record): string => route('projects.show.details', ['project' => $record->slug]);
    }

    public function updatedToggledTableColumns(): void
    {
        // Keep session behavior (so current page updates instantly)
        session()->put([
            $this->getTableColumnToggleFormStateSessionKey() => $this->toggledTableColumns,
        ]);

        // Persist to DB for this user and table
        if ($user = request()->user()) {
            UserTablePreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'table_key' => $this->tablePreferenceKey,
                ],
                [
                    'toggled_columns' => $this->toggledTableColumns,
                ],
            );
        }
    }

    protected function loadUserColumnToggles(): void
    {
        $user = request()->user();
        if (! $user) {
            return;
        }

        $pref = UserTablePreference::where('user_id', $user->id)
            ->where('table_key', $this->tablePreferenceKey)
            ->first();

        if ($pref && is_array($pref->toggled_columns)) {
            $this->toggledTableColumns = $pref->toggled_columns;

            // Seed session so Filament’s form picks it up immediately
            session()->put([
                $this->getTableColumnToggleFormStateSessionKey() => $this->toggledTableColumns,
            ]);
        }
    }

    protected function getCurrentFiltersFromRequest(): array
    {
        return array_filter([
            'cf_id' => request()->input('cf_id'),
            'mf_id' => request()->input('mf_id'),
            'sg_id' => request()->input('sg_id'),
            'st' => request()->input('st'),
        ], fn ($v) => filled($v));
    }

    protected function saveFiltersArrayForUser(array $filters): void
    {
        $user = request()->user();
        if (! $user) return;

        UserTablePreference::updateOrCreate(
            [ 'user_id' => $user->id, 'table_key' => $this->tablePreferenceKey ],
            [ 'saved_filters' => $filters ]
        );
    }

    protected function applySavedFiltersForUser(): void
    {
        $user = request()->user();
        if (! $user) return;

        $pref = UserTablePreference::where('user_id', $user->id)
            ->where('table_key', $this->tablePreferenceKey)
            ->first();

        $filters = $pref?->saved_filters ?? [];
        if (empty($filters)) {
            return;
        }
        $url = request()->url();
        $params = array_merge(request()->query(), $filters);
        $params['page'] = 1;

        $this->redirect($url . (count($params) ? ('?' . http_build_query($params)) : ''), navigate: true);
        return;
    }

    protected function clearSavedFiltersForUser(): void
    {
        $user = request()->user();
        if (! $user) return;

        UserTablePreference::updateOrCreate(
            [ 'user_id' => $user->id, 'table_key' => $this->tablePreferenceKey ],
            [ 'saved_filters' => null ]
        );
    }

    protected function getSavedFiltersForUser(): array
    {
        $user = request()->user();
        if (! $user) return [];

        if (! Schema::hasTable('user_table_preferences') || ! Schema::hasColumn('user_table_preferences', 'saved_filters')) {
            return [];
        }

        return UserTablePreference::where('user_id', $user->id)
            ->where('table_key', $this->tablePreferenceKey)
            ->value('saved_filters') ?? [];
    }

    protected function getFilterParam(string $key)
    {
        return request()->input($key) ?? ($this->activeSavedFilters[$key] ?? null);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->limit(200)
                ->tooltip(fn (Model $record): string => $record->name)
                ->grow(false)
                ->description(function (Model $record): string {
                    return $record->tenant ? $record->tenant->name : 'Projet national';
                })
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query
                        ->orderBy('name', $direction);
                })
                ->searchable()->toggleable(),

            TextColumn::make('certification.name')->label('Certification')->toggleable(),
            TextColumn::make('segmentation.name')
                ->label('Segmentation')
                ->toggleable(isToggledHiddenByDefault: true),
            // Méthode du projet (toggleable, cachée par défaut)
            TextColumn::make('methodForm.name')
                ->label('Méthode')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('state')
                ->label('Statut')
                ->formatStateUsing(fn (Project $record) => $record->state?->humanName() ?? '-')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('sponsor.name')
                ->label('Porteur')
                ->description(function (Model $record): string {
                    return match ($record->sponsor ? get_class($record->sponsor) : null) {
                        User::class => 'Acteur',
                        Organization::class => 'Organisation',
                        default => 'Type de porteur inconnu'
                    };
                })->toggleable(),
            TextColumn::make('auditor.name')
                ->default('-')
                ->label('Auditeur')->toggleable(),
            TextColumn::make('referent.name')
                ->default('-')
                ->label('Réfèrent')->toggleable(),

            TextColumn::make('id')
                ->formatStateUsing(function (Project $record) {
                    /*if ($record->children_projects_count == 0) {
                        return '-';
                    }*/

                    if (!isset($record->cost_global_ttc) || !is_numeric($record->cost_global_ttc)) {
                        return '-'; 
                    }

                    $amountWanted = (float) $record->cost_global_ttc;
                    $contributionsReceived = optional($record->donationSplits)->sum('amount') ?? 0.0;
                    $remainingToFund = $amountWanted - $contributionsReceived;

                    return format(max(0, $remainingToFund), 2).' €';
                })
                ->label('Reste à financer (TTC)')->toggleable(),

            TextColumn::make('created_at')
                ->label('Date de création')
                ->formatStateUsing(function (Project $record): ?string {
                    return "Créer: " . $record->created_at->format('d/m/Y');
                })
                ->sortable(['created_at'])->toggleable(),
            TextColumn::make('plantation_at')
                ->label('Date de plantation')
                ->formatStateUsing(function (Project $record): ?string {
                    return "Planter: " . $record->created_at->format('d/m/Y');
                })
                ->sortable(['plantation_at'])->toggleable(),
        ];
    }


    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('synchronize')
                ->label('Synchroniser avec le projet parent')
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->action(function (Collection $records) {
                    if ($records->count() == 0) {
                        return;
                    }

                    $records->toQuery()->update([
                        'is_synchronized_with_parent' => true
                    ]);

                    defaultSuccessNotification("Tous les projets sélectionnés sont maintenant synchronisés.");
                })
        ];
    }

    protected function getTableHeaderActions(): array { return []; }

protected function isTablePaginationEnabled(): bool
    {
        return (bool) ! $this->project;
    }

    public function render()
    {
        return view('livewire.tables.projects.index-table');
    }
}
