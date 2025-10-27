<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Enums\Roles;
use App\Models\Certification;
use App\Models\Tenant;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class CertificationsTable extends Component implements HasForms, HasTable
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
        return Certification::when(request()->user()->hasRole(Roles::LocalAdmin), function ($query) {
            return $query->where('tenant_id', userTenantId())->orWhereNull('tenant_id');
        });
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Modifier')
                ->mountUsing(function (ComponentContainer $form, Model $record) {
                    $form->fill([
                        'name' => $record->name,
                        'tenant_id' => $record->tenant_id,
                        'image_black_and_white' => str_replace('/storage/', '', $record->image_black_and_white ?? ''),
                        'image' => str_replace('/storage/', '', $record->image ?? ''),
                    ]);
                })
                ->action(function (Model $record, array $data): void {
                    $toBeUpdated = $data;

                    $toBeUpdated['image'] = '/storage/'.$data['image'];
                    $toBeUpdated['image_black_and_white'] = '/storage/'.$data['image_black_and_white'];

                    $record->update($toBeUpdated);
                })
                ->form([
                    Grid::make()
                        ->schema([
                            TextInput::make('name')
                                ->label('Nom')
                                ->columnSpanFull()
                                ->required(),
                            FileUpload::make('image_black_and_white')
                                ->label('Logo pré-certification')
                                ->visibility('public')
                                ->preserveFilenames()
                                ->helperText('Exemple : Logo final en noir et blanc')
                                ->image()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->lazy(),
                            FileUpload::make('image')
                                ->label('Logo post-certification')
                                ->visibility('public')
                                ->preserveFilenames()
                                ->helperText('Exemple : Logo final en couleur')
                                ->image()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->lazy(),
                            Select::make('tenant_id')
                                ->searchable()
                                ->selectablePlaceholder(false)
                                ->label('Antenne locale')
                                ->options(function (Model $record) {
                                    if (request()->user()->hasRole(Roles::Admin)) {
                                        return Tenant::all()->pluck('name', 'id')->toArray();
                                    }

                                    return Tenant::where('id', userTenantId())->get()->pluck('name', 'id')->toArray();
                                }),
                        ]),
                ])
                ->size('sm')
                ->modalHeading('Mise à jour de la certification')
                ->modalSubmitActionLabel('Mettre à jour'),
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
        return view('livewire.tables.settings.certifications-table');
    }
}
