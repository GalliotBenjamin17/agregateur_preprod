<?php

namespace App\Http\Livewire\Interface\Forms\Profile;

use App\Models\Organization;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;

class OrganizationProfileForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    #[Locked]
    public Organization $organization;

    #[Locked]
    public bool $canUpdateForm = false;

    public function mount()
    {
        $this->canUpdateForm = in_array(request()->user()->id, $this->organization->users()->wherePivot('is_organization_manager', true)->pluck('id')->toArray());

        $this->form->fill([
            'organization' => $this->organization,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Mettre à jour mes informations de mon entité')
                ->compact()
                ->schema([
                    Fieldset::make('information')
                        ->label('Informations')
                        ->schema([

                            TextInput::make('organization.name')
                                ->label("Nom de l'entité")
                                ->required()
                                ->disabled(! $this->canUpdateForm)
                                ->autofocus(),

                        ]),

                    Fieldset::make('address')
                        ->label('Adresse')
                        ->schema([
                            TextInput::make('organization.address_1')
                                ->label('Adresse 1')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid())
                                ->autofocus(),
                            TextInput::make('organization.address_2')
                                ->label('Complément')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid()),
                            TextInput::make('organization.address_postal_code')
                                ->label('Code postal')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid()),
                            TextInput::make('organization.address_city')
                                ->label('Ville')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid()),
                        ]),

                    Fieldset::make('payment_information')
                        ->label('Informations de paiements')
                        ->schema([
                            TextInput::make('organization.billing_email')
                                ->label('Email de facturation')
                                ->disabled(! $this->canUpdateForm)
                                ->email()
                                ->autocomplete(Str::orderedUuid()),

                        ]),

                    Fieldset::make('legal_information')
                        ->label('Informations légales')
                        ->schema([
                            TextInput::make('organization.legal_siret')
                                ->label('SIRET')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid()),
                            TextInput::make('organization.legal_siren')
                                ->label('SIREN')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid()),
                            DatePicker::make('organization.legal_created_at')
                                ->label('Date de création')
                                ->disabled(! $this->canUpdateForm)
                                ->displayFormat('d/m/Y'),
                            TextInput::make('organization.legal_name')
                                ->label('Nom légal')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid()),
                            TextInput::make('organization.legal_activity_code')
                                ->label('Code activité')
                                ->disabled(! $this->canUpdateForm)
                                ->autocomplete(Str::orderedUuid()),
                            Toggle::make('organization.legal_is_ess')
                                ->inline(false)
                                ->disabled(! $this->canUpdateForm)
                                ->label('Entreprise Sociale et solidaire'),
                        ]),
                ]),

        ];
    }

    public function submit(): void
    {
        $this->organization->update($this->form->getState()['organization']);

        defaultSuccessNotification(
            title: 'Le profil de votre entité a été mis à jour.'
        );
    }

    public function render()
    {
        return view('livewire.interface.forms.profile.organization-profile-form');
    }
}
