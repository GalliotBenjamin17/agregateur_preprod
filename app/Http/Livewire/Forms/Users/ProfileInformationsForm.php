<?php

namespace App\Http\Livewire\Forms\Users;

use App\Enums\Roles;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Models\Organization; // Import Organization model
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

class ProfileInformationsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public User $user;
    public array $tenants = [];
    public array $organizations = []; // This will hold the IDs of selected organizations for the multiple select

    public function mount()
    {
        $this->tenants = Tenant::all()->pluck('name', 'id')->toArray();

        $this->form->fill([
            'user'          => $this->user, // Fill the 'user' data
            'roles'         => $this->user->roles->pluck('name')->toArray(), // Fill roles
            'avatar'        => str_replace('/storage/', '', $this->user->avatar ?? ''),
            'organizations' => $this->user->organizations->pluck('id')->toArray(),
        ]);

        //$this->organizations = $this->user->organizations->pluck('name')->toArray();

    }

    protected function getFormSchema(): array
    {
        return [

            Fieldset::make('denomination')
                ->label('Contact')
                ->extraAttributes(['id' => 'toto'])
                ->schema([
                    TextInput::make('user.last_name')
                        ->label('Nom')
                        ->lazy()
                        ->required(),
                    TextInput::make('user.first_name')
                        ->label('Prénom')
                        ->lazy()
                        ->required()
                        ->autofocus(),
                    TextInput::make('user.email')
                        ->label('Email principale')
                        ->lazy()
                        ->email()
                        ->required()
                        ->placeholder('prenom.nom@email.com'),
                    TextInput::make('user.phone')
                        ->label('Téléphone')
                        ->lazy()
                        ->tel()
                        ->mask('+99.99.99.99.99'),
                    TextInput::make('user.address_1')
                        ->label('Adresse 1')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid())
                        ->placeholder('Rue de la poste')
                        ->autofocus(),
                    TextInput::make('user.address_2')
                        ->label('Complément adresse')
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

            /*Fieldset::make('contact')
            ->label('Contact')
            ->schema([

            ]),

            Fieldset::make('address')
            ->label('Adresse postale (personnelle ou professionnelle)')
            ->schema([

            ]),*/

            Fieldset::make('relations')
                ->label('Relations')
                ->schema([
                    Select::make('organizations')
                        ->multiple()
                        ->label('Organisation(s)')
                        ->placeholder('Sélectionnez une ou plusieurs organisations')
                        ->options(
                            Organization::where('tenant_id', $this->user->tenant_id)->pluck('name', 'id') // Provide all available organizations for the current tenant
                        )
                        ->searchable() // Enable searching for better UX
                        ->preload(), // Load all options initially
                    Select::make('user.tenant_id')
                        ->visible(request()->user()->hasRole(Roles::Admin))
                        ->options($this->tenants)
                        ->label('Instance locale'),
                ]),

            Fieldset::make('options')
                ->label('Visibilité / Synchronisation')
                ->schema([
                    FileUpload::make('avatar')
                        ->preserveFilenames()
                        ->label('Avatar')
                        ->visibility('public')
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->lazy()
                        ->helperText("Taille maximale de l'image: 500ko")
                        ->avatar()
                        ->maxSize(512),
                    Select::make('roles')
                        ->visible($this->canDisplayRoles())
                        ->multiple()
                        ->placeholder('Sélectionnez un rôle')
                        ->options(Roles::toSelect())
                        ->label('Rôle')
                        ->helperText('Un ou plusieurs rôles'),

                    Toggle::make('user.can_be_displayed_on_website')
                        ->visible($this->canDisplayRoles())
                        ->label('Affiché sur le site web')
                        ->visible($this->user->hasTenant())
                        ->lazy(),

                    Toggle::make('user.is_shareholder')
                        ->visible($this->canDisplayRoles())
                        ->lazy()
                        ->label('Sociétaire'),
                ]),

            Fieldset::make('payment')
                ->label('Informations de paiement')
                ->visible($this->user->hasAnyRole(Roles::Sponsor))
                ->schema([
                    TextInput::make('user.iban')
                        ->label('IBAN')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid())
                        ->placeholder('FR...'),
                    TextInput::make('user.bic')
                        ->label('BIC')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid()),
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
        ];
    }

    protected function canDisplayRoles(): bool
    {
        return request()->user()->hasAnyRole([Roles::Admin, Roles::LocalAdmin]) && !$this->user->hasAnyRole([Roles::Admin, Roles::LocalAdmin]);
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        if ($this->canDisplayRoles()) {
            $this->user->syncRoles($state['roles']);
        }
        
        // Sync the organizations relationship (BelongsToMany)
        $this->user->organizations()->sync($state['organizations'] ?? []);

        $this->user->update(array_merge($state['user'], [
            'avatar' => $state['avatar'] ? '/storage/' . $state['avatar'] : null, // Correctly handle avatar path or null
        ]));

        Notification::make()
            ->title('Profil mis à jour')
            ->body("Les informations saisies ont été mises à jour sur l'ensemble de la plateforme.")
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.forms.users.profile-informations-form');
    }
}
