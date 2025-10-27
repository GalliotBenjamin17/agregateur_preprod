<?php

namespace App\Http\Livewire\Actions\Partners;

use App\Models\Partner;
use App\Models\Tenant;
use App\Services\Models\PartnersService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class CreateForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Tenant $tenant = null;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajouter un partenaire')
            ->size('sm')
            ->form([
                Grid::make()
                    ->schema([
                        Select::make('tenant_id')
                            ->label('Instance locale')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->required(is_null($this->tenant))
                            ->visible(is_null($this->tenant))
                            ->relationship('tenant', 'name'),

                        TextInput::make('name')
                            ->label('Nom')
                            ->columnSpanFull()
                            ->required(),
                    ]),
            ])
            ->action(function (array $data) {
                $partnersService = new PartnersService();
                $partnersService->store([
                    ...$data,
                    'tenant_id' => $data['tenant_id'] ?? $this->tenant->id,
                ]);

                $this->dispatch('partnerAdded');

                Notification::make()
                    ->title('Partenaire ajouté')
                    ->body('Vous pouvez accéder aux détails dans la table sur cette même page')
                    ->success()
                    ->send();
            })
            ->model(Partner::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.partners.create-form');
    }
}
