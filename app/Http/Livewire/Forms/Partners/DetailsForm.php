<?php

namespace App\Http\Livewire\Forms\Partners;

use App\Helpers\ActivityHelper;
use App\Models\Partner;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

class DetailsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Partner $partner;

    public function mount()
    {
        $this->form->fill([
            'partner'  => $this->partner,
            'contacts' => $this->partner->contacts ?? [],
            'avatar'       => str_replace('/storage/', '', $this->partner->avatar ?? ''),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('information')
                ->label('Informations')
                ->columns(2)
                ->schema([
                    Grid::make(1)
                        ->columnSpan(1)
                        ->schema([
                            TextInput::make('partner.name')
                                ->label('Nom')
                                ->required()
                                ->autofocus(),

                        ]),
                    FileUpload::make('avatar')
                        ->preserveFilenames()
                        ->label('Avatar')
                        ->visibility('public')
                        ->image()
                        ->lazy()
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->columnSpan(1)
                        ->avatar(),
                    TextInput::make('partner.email')
                        ->label('Email principal')
                        ->email()
                        ->lazy(),
                    TextInput::make('partner.phone')
                        ->label('Téléphone principal')
                        ->lazy()
                        ->tel()
                        ->mask('99.99.99.99.99'),
                ]),

            Fieldset::make('contact_informations')
                ->label('')
                ->schema([
                    Repeater::make('contacts')
                        ->label('Ajout contact(s)')
                        ->lazy()
                        ->helperText("Attention : Un contact n'a pas accès au logiciel, il s'agit seulement d'une section type annuaire. Pour ajouter un utilisateur à ce partenaire, accédez à la section : 'Utilisateurs'.")
                        ->schema([
                            TextInput::make('name')
                                ->label('Nom du contact')
                                ->lazy()
                                ->required(),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->lazy(),
                            TextInput::make('phone')
                                ->label('Téléphone')
                                ->lazy()
                                ->tel()
                                ->mask('99.99.99.99.99'),
                        ])->columns(3)
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                        ->addActionLabel('Ajouter un contact')
                        ->collapsible()
                        ->columnSpanFull(),
                ]),

            Fieldset::make()
                ->label('Informations de facturation')
                ->schema([
                    TextInput::make('partner.billing_address')
                        ->label('Adresse 1')
                        ->autocomplete(Str::orderedUuid())
                        ->autofocus(),
                    TextInput::make('partner.billing_address_2')
                        ->label('Complément')
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('partner.billing_address_zip_code')
                        ->label('Code postal')
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('partner.billing_address_city')
                        ->label('Ville')
                        ->autocomplete(Str::orderedUuid()),

                    TextInput::make('partner.billing_email')
                        ->columnSpanFull()
                        ->prefixIcon('heroicon-s-at-symbol')
                        ->label('Email de facturation')
                        ->email()
                        ->autocomplete(Str::orderedUuid()),
                ]),

            Fieldset::make('legal_information')
                ->label('Informations légales')
                ->schema([
                    TextInput::make('partner.legal_siret')
                        ->label('SIRET')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('partner.legal_siren')
                        ->label('SIREN')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    DatePicker::make('partner.legal_created_at')
                        ->label('Date de création')
                        ->displayFormat('d/m/Y'),
                    TextInput::make('partner.legal_name')
                        ->label('Nom légal')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('partner.legal_activity_code')
                        ->label('Code activité')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                ]),
        ];
    }

    public function submit()
    {
        /*$this->partner->update([
             ...$this->form->getState()['partner'],
            'avatar'   => '/storage/' . $this->form->getState()['avatar'],
            'contacts' => $this->form->getState()['contacts'],
        ]);*/

        $this->partner->update(array_merge($this->form->getState()['partner'], [
            'avatar'   => '/storage/' . $this->form->getState()['avatar'],
            'contacts' => $this->form->getState()['contacts'],
        ]));

        Notification::make()
            ->title('Informations mise à jour.')
            ->body("Le partenaire a été mis à jour et l'ensemble des informations sur les autres pages également.")
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->partner,
            title: 'Mise à jour du partenaire',
            url: route('partners.show', ['partner' => $this->partner])
        );
    }

    public function render()
    {
        return view('livewire.forms.partners.details-form');
    }
}
