<?php

namespace App\Http\Livewire\Tables\Partners;

use App\Enums\Roles;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Tenant;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class IndexTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public ?Project $project = null;

    public ?Tenant $tenant = null;

    protected $listeners = [
        'partnerAdded' => 'render',
    ];

    protected function getTableQuery(): Builder
    {
        return Partner::with([
            'tenant',
        ])
            ->when($this->tenant, function ($query) {
                return $query->where('tenant_id', $this->tenant->id);
            })
            ->when(request()->user()->hasRole(Roles::Partner), function ($query) {
                return $query->whereIn('id', request()->user()->partners()->pluck('id')->toArray());
            });
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('tenant_filter')
                ->searchable()
                ->preload()
                ->visible(is_null($this->tenant))
                ->label('Instance locale')
                ->relationship('tenant', 'name'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('name')
                ->searchable()
                ->label('Nom'),

            /*TextColumn::make('tenant.name')
                ->visible(is_null($this->tenant))
                ->label('Instance locale'),*/

            TextColumn::make('createdBy.name')
                ->label('Création par')
                ->default('Inconnu')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('d/m/Y H:i');
                })
                ->sortable(['created_at']),
        ];
    }

    protected function getTableRecordUrlUsing(): \Closure
    {
        return fn(Partner $record): string => route('partners.show', ['partner' => $record]);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return (bool) !$this->tenant;
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
        return view('livewire.tables.partners.index-table');
    }
}
