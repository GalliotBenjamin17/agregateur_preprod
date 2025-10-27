<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\MethodForm;
use App\Models\MethodFormGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class MethodFormTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public MethodFormGroup $methodFormGroup;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return MethodForm::with([
            'lockedBy',
        ])->where('method_form_group_id', $this->methodFormGroup->id);
    }

    protected function getTableRecordUrlUsing(): \Closure
    {
        return fn (MethodForm $record): string => route('settings.method-form-groups.method-form.show', ['methodFormGroup' => $record->methodFormGroup->slug, 'methodForm' => $record->id]);
    }

    protected function getTableActions(): array
    {
        return [
            ReplicateAction::make()
                ->beforeReplicaSaved(function (MethodForm $replica): void {
                    $replica->id = \Str::orderedUuid();
                    $replica->name = $replica->name.' - Copie';
                    $replica->slug = $replica->name.Str::random(8);
                    $replica->locked_at = null;
                    $replica->locked_by = null;
                    $replica->created_by = request()->user()->id;
                }),

        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->searchable()
                ->sortable(),

            IconColumn::make('id')
                ->icon(function (MethodForm $record) {
                    if ($record->id == $this->methodFormGroup->active_method_form_id) {
                        return 'heroicon-o-check-circle';
                    }

                    return 'heroicon-o-x-circle';
                })
                ->label('Actif ?')
                ->color(function (MethodForm $record) {
                    if ($record->id == $this->methodFormGroup->active_method_form_id) {
                        return 'success';
                    }

                    return 'danger';
                }),

            TextColumn::make('lockedBy.name')
                ->label('Bloquée par')
                ->description(function (Model $record): ?string {
                    return $record->locked_at?->format('\A H:i \l\e d/m/Y');
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

    public function render()
    {
        return view('livewire.tables.settings.method-form-table');
    }
}
