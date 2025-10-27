<?php

namespace App\Http\Livewire\Tables\Badges;

use App\Models\Badge;
use App\Services\Models\BadgesService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Component;

class IndexTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected $listeners = [
        'badgeAdded' => 'render',
    ];

    protected function getTableQuery(): Builder
    {
        return Badge::withCount('organizations');
    }

    protected function getTableActions(): array
    {
        return [

            Action::make('update')
                ->label('Mettre à jour')
                ->mountUsing(function (Badge $badge, Form $form) {
                    $form->fill([
                        'badge' => $badge->toArray(),
                    ]);
                })
                ->slideOver()
                ->form([

                    TextInput::make('badge.name')
                        ->columnSpanFull()
                        ->label('Nom')
                        ->required(),

                    Textarea::make('badge.description')
                        ->label('Description'),

                    FileUpload::make('badge.picture')
                        ->openable()
                        ->downloadable()
                        ->image()
                        ->required()
                        ->imageEditor()
                        ->preserveFilenames()
                        ->disk('public')
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->label('Icône'),
                ])
                ->action(function (Badge $badge, $data) {

                    $badgeService = new BadgesService(badge: $badge);
                    $badgeService->update($data['badge']);

                    defaultSuccessNotification();
                }),

            Action::make('delete')
                ->label('Supprimer')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (Badge $badge) {

                    $badge->delete();

                    defaultSuccessNotification();

                }),

        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->description(function (Badge $badge) {
                    return Str::limit($badge->description ?? 'Aucune description', 50);
                })
                ->label('Nom')
                ->searchable(),

            ImageColumn::make('picture')
                ->disk('public')
                ->circular()
                ->label('Image'),

            TextColumn::make('organizations_count')
                ->label("Nombre d'organisations")
                ->counts('organizations')
                ->sortable(),
        ];
    }

    public function render()
    {
        return view('livewire.tables.badges.index-table');
    }
}
