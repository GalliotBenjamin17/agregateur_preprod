<?php

namespace App\Http\Livewire\Tables\FinancialExports;

use App\Models\Project;
use App\Models\ProjectFinancialExport;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class IndexTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Project $project = null;

    protected $listeners = [
        'financialExportAdded' => 'render',
        'financialExportsAdded' => 'render',
    ];

    protected function getTableQuery(): Builder
    {

        return ProjectFinancialExport::with('generatedBy')
            ->when($this->project, function ($query) {
                return $query->where('project_id', $this->project->id);
            });
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableFilters(): array
    {
        return [
        ];
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('generatedBy.name')
                ->label('Généré par'),

            TextColumn::make('generated_at')
                ->label('Généré à')
                ->dateTime(),

            TextColumn::make('file_path')
                ->label('Fichier')
                ->formatStateUsing(function () {
                    return new HtmlString("<span class='text-blue-500'>Télécharger</span>");
                })
                ->url(function ($state) {
                    return 'storage/'.$state;
                }, shouldOpenInNewTab: false),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return (bool) ! $this->project;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function render()
    {
        return view('livewire.tables.financial-exports.index-table');
    }
}
