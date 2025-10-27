<?php

namespace App\Http\Livewire\Interface\Forms\Profile;

use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class NotificationsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public function mount()
    {
        $this->form->fill([
            'user' => request()->user()->toArray(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Mettre à jour mes préférences de notification')
                ->compact()
                ->schema([

                    Fieldset::make('Visibilité sur le site')
                        ->schema([

                            Toggle::make('user.can_be_displayed_on_website')
                                ->columnSpanFull()
                                ->onColor('success')
                                ->label('Je souhaite être visible dans la liste des contributeurs sur le site web (page ils agissent avec nous)'),

                        ]),

                    Fieldset::make('Emails')
                        ->schema([

                            Toggle::make('user.can_be_notified_marketing')
                                ->columnSpanFull()
                                ->onColor('success')
                                ->label("Je souhaite recevoir par email toutes les informations sur les projets auxquels j'ai contribué"),

                        ]),

                ]),

        ];
    }

    public function submit(): void
    {
        request()->user()->update($this->form->getState()['user']);

        defaultSuccessNotification(
            title: 'Votre profil a été mis à jour'
        );
    }

    public function render()
    {
        return view('livewire.interface.forms.profile.notifications-form');
    }
}
