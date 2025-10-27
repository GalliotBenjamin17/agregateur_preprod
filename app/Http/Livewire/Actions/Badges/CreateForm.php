<?php

namespace App\Http\Livewire\Actions\Badges;

use App\Models\Project;
use App\Models\Tenant;
use App\Services\Models\BadgesService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class CreateForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Project $project = null;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajouter un badge')
            ->size('sm')
            ->icon('heroicon-m-plus-circle')
            ->form([

                Grid::make(2)
                    ->schema([

                        Select::make('tenant_id')
                            ->required()
                            ->searchable()
                            ->options(Tenant::pluck('name', 'id')->toArray())
                            ->label('Antenne locale'),

                        TextInput::make('name')
                            ->label('Nom')
                            ->required(),

                        Textarea::make('description')
                            ->label('Description'),

                        FileUpload::make('picture')
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
                    ]),

            ])
            ->action(function (Component $livewire, array $data) {

                $badgeService = new BadgesService();
                $badgeService->store($data);

                $this->dispatch('badgeAdded');

                Notification::make()
                    ->title('Badge ajouté')
                    ->body("Vous pouvez maintenant l'affilier sur une entité.")
                    ->success()
                    ->send();

            })
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.badges.create-form');
    }
}
