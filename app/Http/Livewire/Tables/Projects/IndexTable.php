<?php

namespace App\Http\Livewire\Tables\Projects;

use App\Enums\Models\Projects\ProjectStateEnum;
use App\Enums\Roles;
use App\Models\Certification;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\BulkAction;
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
            });
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('tenant_filter')
                ->searchable()
                ->visible(request()->user()->hasRole(Roles::Admin))
                ->label('Antenne locale')
                ->preload()
                ->relationship('tenant', 'name'),

            SelectFilter::make('certification_filter_3')
                ->label('Certification')
                ->attribute('certification_id')
                ->multiple()
                ->options(Certification::pluck('name', 'id')),

            SelectFilter::make('segmentation_filter')
                ->multiple()
                ->label('Segmentation')
                ->preload()
                ->relationship('segmentation', 'name'),
            SelectFilter::make('method_filter')
                ->multiple()
                ->label('Méthode')
                ->preload()
                ->visible(request()->user()->hasRole(Roles::Admin, Roles::LocalAdmin))
                ->relationship('methodForm', 'name'),
            SelectFilter::make('state_filter')
                ->attribute('state')
                ->multiple()
                ->label('Statut')
                ->options(ProjectStateEnum::toArray()),
            Filter::make('can_be_displayed_on_website')
                ->toggle()
                ->query(fn (Builder $query): Builder => $query->where('can_be_displayed_on_website', true))
                ->label('Visible en ligne'),
            Filter::make('can_be_financed_online')
                ->toggle()
                ->query(fn (Builder $query): Builder => $query->where('can_be_financed_online', true))
                ->label('Finançable en ligne'),

            Filter::make('available_money')
                ->toggle()
                ->query(function (Builder $query): Builder {
                    // Nouvelle logique : Filtrer les projets où le montant recherché
                    // moins les contributions reçues est supérieur à 0.
                    return $query->whereExists(function ($query) {
                        $query->select(\DB::raw(1))
                            ->from('projects as p') 
                            ->leftJoin('donation_splits as ds', 'p.id', '=', 'ds.project_id') 
                            ->whereColumn('p.id', 'projects.id') 
                            ->whereNotNull('p.amount_wanted_ttc') 
                            ->where('p.amount_wanted_ttc', '>', 0) 
                            ->groupBy('p.id', 'p.amount_wanted_ttc') 
                            ->havingRaw('p.amount_wanted_ttc - COALESCE(SUM(ds.amount), 0) > 0');
                    });
                })
                ->label('Reste à financer > 0'),
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Model $record): string => route('projects.show.details', ['project' => $record->slug]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->limit(50)
                ->tooltip(fn (Model $record): string => $record->name)
                ->grow(false)
                ->description(function (Model $record): string {
                    return $record->tenant ? $record->tenant->name : 'Projet national';
                })
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query
                        ->orderBy('name', $direction);
                })
                ->searchable(),

            TextColumn::make('certification.name')
                ->visible(is_null($this->project))
                ->default('-')
                ->description(function (Model $record): string {
                    return $record->state->humanName();
                })
                ->label('Certification'),
            TextColumn::make('sponsor.name')
                ->label('Porteur')
                ->description(function (Model $record): string {
                    return match ($record->sponsor ? get_class($record->sponsor) : null) {
                        User::class => 'Acteur',
                        Organization::class => 'Organisation',
                        default => 'Type de porteur inconnu'
                    };
                }),
            TextColumn::make('auditor.name')
                ->default('-')
                ->label('Auditeur'),
            TextColumn::make('referent.name')
                ->default('-')
                ->label('Référent'),

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
                ->label('Reste à financer (TTC)'),

            TextColumn::make('created_at')
                ->label('Démarrage et dépôt')
                ->formatStateUsing(function (Project $record): ?string {
                    return "Démarrage : " . $record->created_at->format('d/m/Y');
                })
                ->description(function (Model $record): ?string {
                    return 'Déposé le '.$record->created_at->format('d/m/Y');
                })
                ->sortable(['created_at']),
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

    protected function isTablePaginationEnabled(): bool
    {
        return (bool) ! $this->project;
    }

    public function render()
    {
        return view('livewire.tables.projects.index-table');
    }
}
