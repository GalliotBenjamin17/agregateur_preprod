<?php

namespace App\Http\Livewire\Actions\Organizations;

use App\Enums\Roles;
use App\Models\Organization;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class BadgesForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Organization $organization;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Modifier')
            ->size('sm')
            ->visible(request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
            ->mountUsing(function (Form $form) {
                $form->fill([
                    'badges_id' => $this->organization->badges()->pluck('id')->toArray(),
                ]);
            })
            ->form([
                Grid::make()
                    ->schema([
                        Select::make('badges_id')
                            ->label('Badges')
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->columnSpanFull()
                            ->relationship('badges', 'name', function ($query) {
                                return $query->when(userHasTenant(), function ($query) {
                                    return $query->where('tenant_id', userTenantId());
                                });
                            }),
                    ]),
            ])
            ->action(function (Component $livewire, array $data) {

                defaultSuccessNotification('Les badges ont été modifié sur cette organisation.');

                return to_route('organizations.show', ['organization' => $this->organization]);
            })
            ->model(Organization::class)
            ->record($this->organization)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.organizations.badges-form');
    }
}
