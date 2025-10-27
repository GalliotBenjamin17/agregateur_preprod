<?php

namespace App\Http\Livewire\Forms\Organizations;

use App\Helpers\ActivityHelper;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Tenant;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

    public Organization $organization;

    public array $contacts = [];

    public array $organizationTypes = [];

    public array $organizations = [];

    public array $tenants = [];

    public function mount()
    {
        $this->organizationTypes = OrganizationType::all()
            ->pluck('name', 'id')
            ->toArray();

        $this->organizations = Organization::select(['id', 'name', 'tenant_id'])
            ->where('tenant_id', $this->organization->tenant_id)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->tenants = Tenant::select(['id', 'name'])->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->form->fill([
            'organization' => $this->organization,
            'avatar'       => str_replace('/storage/', '', $this->organization->avatar ?? ''),
            'contacts'     => $this->organization->contacts ?? [],
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('information')
                ->label('Informations')
                ->schema([
                    TextInput::make('organization.name')
                        ->label("Nom de l'entité")
                        ->lazy()
                        ->required()
                        ->autofocus(),

                    Select::make('organization.organization_type_id')
                        ->label("Type d'entité")
                        ->searchable()
                        ->options($this->organizationTypes)
                        ->required(),

                    RichEditor::make('organization.description')
                        ->placeholder('Autres informations utiles sur l\'entité ...')
                        ->disableToolbarButtons(['attachFiles', 'codeBlock', 'h2', 'h3', 'link', 'attachFiles', 'blockquote', 'strike'])
                        ->columnSpanFull()
                        ->lazy(),
                    TextInput::make('organization.address_1')
                        ->label('Adresse 1')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid())
                        ->autofocus(),
                    TextInput::make('organization.address_2')
                        ->label('Complément')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('organization.address_postal_code')
                        ->label('Code postal')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('organization.address_city')
                        ->label('Ville')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                ]),

            Fieldset::make('relations')
                ->label('Relation')
                ->schema([
                    Select::make('organization.tenant_id')
                        ->label('Antenne locale')
                        ->options($this->tenants),

                    Select::make('organization.organization_parent_id')
                        ->label('Organisation parente')
                        ->options($this->organizations),
                ]),

            Fieldset::make('contact_informations')
                ->label('Informations de contacts')
                ->schema([
                    Repeater::make('contacts')
                        ->label('Informations de contact')
                        ->lazy()
                        ->helperText("Attention : Un contact n'a pas accès au logiciel, il s'agit seulement d'une section type annuaire. Pour ajouter un utilisateur à l'organisation, accédez à la section : 'Utilisateurs'.")
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

            Fieldset::make('management')
                ->label('Visibilité')
                ->columns(2)
                ->schema([
                    Grid::make(1)
                        ->schema([
                            Toggle::make('organization.can_be_displayed_on_website')
                                ->label('Affiché sur le site internet'),

                            Toggle::make('organization.is_shareholder')
                                ->label('Est sociétaire'),
                        ])
                        ->columnSpan(1),

                    FileUpload::make('avatar')
                        ->preserveFilenames()
                        ->label('Avatar')
                        ->visibility('public')
                        ->image()
                        ->lazy()
                        ->columnSpan(1) // Prend l'autre colonne
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->avatar(),
                ]),

            /*Fieldset::make('address')
                ->label('Adresse')
                ->schema([

                ]),*/

            Fieldset::make('payment_information')
                ->label('Informations de paiements')
                ->schema([
                    TextInput::make('organization.billing_email')
                        ->label('Email de facturation')
                        ->lazy()
                        ->email()
                        ->autocomplete(Str::orderedUuid()),

                ]),

            Fieldset::make('legal_information')
                ->label('Informations légales')
                ->schema([
                    TextInput::make('organization.legal_siret')
                        ->label('SIRET')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('organization.legal_siren')
                        ->label('SIREN')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    DatePicker::make('organization.legal_created_at')
                        ->label('Date de création')
                        ->displayFormat('d/m/Y'),
                    TextInput::make('organization.legal_name')
                        ->label('Nom légal')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    TextInput::make('organization.legal_activity_code')
                        ->label('Code activité')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
                    Toggle::make('organization.legal_is_ess')
                        ->inline(false)
                        ->label('Entreprise Sociale et solidaire'),
                ]),
        ];
    }

    public function submit()
    {
        $this->organization->update(array_merge($this->form->getState()['organization'], [
            'avatar'   => '/storage/' . $this->form->getState()['avatar'],
            'contacts' => $this->form->getState()['contacts'],
        ]));

        Notification::make()
            ->title('Informations mise à jour.')
            ->body("L'organisation a été mise à jour et l'ensemble des informations sur les autres pages également.")
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->organization,
            title: "Mise à jour de l'organisation",
            url: route('organizations.show.details', ['organization' => $this->organization])
        );
    }

    public function render()
    {
        return view('livewire.forms.organizations.details-form');
    }
}
