<?php

namespace App\Http\Livewire\Interface\Forms\Profile;

use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Str;
use Livewire\Component;

class UserProfileForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public function mount()
    {
        $this->form->fill([
            'user' => request()->user()->toArray(),
            'avatar' => str_replace('/storage/', '', request()->user()->avatar ?? ''),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Mettre à jour mes informations')
                ->compact()
                ->schema([

                    FileUpload::make('avatar')
                        ->preserveFilenames()
                        ->label('Avatar')
                        ->visibility('public')
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->helperText("Taille maximale de l'image: 500ko")
                        ->columnSpanFull()
                        ->avatar()
                        ->disk('public')
                        ->maxSize(512),

                    Fieldset::make('denomination')
                        ->label('Dénomination')
                        ->schema([
                            TextInput::make('user.first_name')
                                ->label('Prénom')
                                ->lazy()
                                ->required()
                                ->autofocus(),
                            TextInput::make('user.last_name')
                                ->label('Nom')
                                ->lazy()
                                ->required(),
                        ]),
                    Fieldset::make('contact')
                        ->label('Contact')
                        ->schema([

                            TextInput::make('user.email')
                                ->label('Email principal')
                                ->lazy()
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->placeholder('prenom.nom@email.com'),

                            TextInput::make('user.phone')
                                ->label('Téléphone')
                                ->lazy()
                                ->tel()
                                ->mask('+99.99.99.99.99'),
                        ]),

                    Fieldset::make('address')
                        ->label('Adresse postale (personnelle ou professionnelle)')
                        ->schema([
                            TextInput::make('user.address_1')
                                ->label('Adresse 1')
                                ->lazy()
                                ->autocomplete(Str::orderedUuid())
                                ->placeholder('Rue de la poste')
                                ->autofocus(),
                            TextInput::make('user.address_2')
                                ->label('Complément')
                                ->lazy()
                                ->autocomplete(Str::orderedUuid()),
                            TextInput::make('user.address_postal_code')
                                ->label('Code postal')
                                ->lazy()
                                ->autocomplete(Str::orderedUuid())
                                ->placeholder('69006'),
                            TextInput::make('user.address_city')
                                ->label('Ville')
                                ->lazy()
                                ->autocomplete(Str::orderedUuid())
                                ->placeholder('Lyon'),
                        ]),

                    Fieldset::make('notifications')
                        ->label('Notifications')
                        ->schema([
                            Toggle::make('user.can_be_notified_transactional')
                                ->label('Peut recevoir des notifications transactionnelles')
                                ->lazy(),
                            Toggle::make('user.can_be_notified_marketing')
                                ->label('Peut recevoir des notifications marketing')
                                ->lazy(),
                        ]),
                ]),

        ];
    }

    public function submit(): void
    {
        request()->user()->update(array_merge($this->form->getState()['user'], [
            'avatar' => '/storage/'.$this->form->getState()['avatar'],
        ]));

        defaultSuccessNotification(
            title: 'Votre profil a été mis à jour'
        );
    }

    public function render()
    {
        return view('livewire.interface.forms.profile.user-profile-form');
    }
}
