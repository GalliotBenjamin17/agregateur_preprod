<?php

namespace App\Http\Livewire\Tables\Files;

use App\Models\File;
use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class IndexTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?string $type = null;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return File::with([
            'createdBy',
            'related',
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
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Model $record): string => route('files.show', ['file' => $record->slug]);
    }

    protected function getTableColumns(): array
    {
        return [
            ViewColumn::make('name')
                ->label('Nom du fichier')
                ->searchable()
                ->view('vendor.filament.columns.file-extension'),
            ViewColumn::make('related')
                ->label('Partagé avec')
                ->view('vendor.filament.columns.related'),
            TextColumn::make('createdBy.name')
                ->label('Ajouté par'),
            TextColumn::make('created_at')
                ->label('Ajout')
                ->sortable()
                ->dateTime()
                ->since(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
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
        return view('livewire.tables.files.index-table');
    }
}
