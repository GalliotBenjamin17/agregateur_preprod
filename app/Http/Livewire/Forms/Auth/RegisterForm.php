<?php

namespace App\Http\Livewire\Forms\Auth;

use App\Actions\FetchSiretInformation;
use App\Enums\Roles;
use App\Models\Organization;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Models\UserService;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;

class RegisterForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;
    use UsesSpamProtection;

    #[Locked]
    public Tenant $tenant;

    public HoneypotData $extraFields;

    public function mount()
    {
        $this->extraFields = new HoneypotData();

        $this->form->fill([
            'account_type' => 'individual',
        ]);
    }

    public function siretExistsAction(): Action
    {
        return Action::make('siretExists')
            ->modal()
            ->modalHeading('SIRET déjà utilisé')
            ->modalDescription(new HtmlString("<span class='font-semibold'>Le SIRET que vous avez essayé d'utiliser est déjà présent dans notre base de données. <br>Veuillez contacter la Coopérative Carbone via le formulaire de contact afin d'être ajouté à cette organisation.</span>"))
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('danger')
            ->color('danger')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalWidth('xl');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    ToggleButtons::make('account_type')
                        ->label('Type de compte')
                        ->extraAttributes([
                            'class' => 'w-full'
                        ], merge: true)
                        ->grouped()
                        ->options([
                            'individual' => 'Particulier',
                            'company' => 'Organisation',
                        ])
                        ->icons([
                            'individual' => 'heroicon-o-user',
                            'company' => 'heroicon-o-building-office',
                        ])
                        ->inline()
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    // Individual fields

                    Section::make("Particulier")
                        ->compact()
                        ->columns(2)
                        ->visible(fn (Get $get) => $get('account_type') === 'individual')
                        ->schema([

                            TextInput::make('first_name')
                                ->label('Prénom')
                                ->required()
                                ->maxLength(255)
                                ->visible(fn (Get $get) => $get('account_type') === 'individual'),

                            TextInput::make('last_name')
                                ->label('Nom')
                                ->required()
                                ->maxLength(255)
                                ->visible(fn (Get $get) => $get('account_type') === 'individual'),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->columnSpanFull()
                                ->required()
                                ->prefixIcon('heroicon-s-at-symbol')
                                ->unique(User::class, 'email')
                                ->maxLength(255)
                                ->visible(fn (Get $get) => $get('account_type') === 'individual'),

                            TextInput::make('password')
                                ->label('Mot de passe')
                                ->prefixIcon('heroicon-s-lock-closed')
                                ->columnSpanFull()
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->visible(fn (Get $get) => $get('account_type') === 'individual'),

                        ]),

                    Section::make("Organisation")
                        ->compact()
                        ->columns(2)
                        ->visible(fn (Get $get) => $get('account_type') === 'company')
                        ->schema([

                            // Company fields
                            TextInput::make('company_name')
                                ->label('Nom de l\'organisation')
                                ->required()
                                ->maxLength(255)
                                ->visible(fn (Get $get) => $get('account_type') === 'company'),

                            TextInput::make('company_registration')
                                ->label('Numéro SIRET')
                                ->maxLength(255)
                                ->required()
                                ->unique(Organization::class, 'legal_siret')
                                ->validationMessages([
                                    'unique' => 'Le SIRET que vous avez essayé d\'utiliser est déjà présent dans notre base de données. Veuillez contacter la Coopérative Carbone via le formulaire de contact afin d\'être ajouté à cette organisation.',
                                ])
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (TextInput $component, $state) {
                                    if (empty($state)) {
                                        return;
                                    }

                                    $exists = Organization::where('legal_siret', $state)->exists();

                                    if ($exists) {
                                        $this->mountAction('siretExists');
                                    }
                                })
                                ->visible(fn (Get $get) => $get('account_type') === 'company'),

                            Fieldset::make('Représentant')
                                ->schema([
                                    TextInput::make('representative_first_name')
                                        ->label('Prénom')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('representative_last_name')
                                        ->label('Nom')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('representative_email')
                                        ->label('Email')
                                        ->email()
                                        ->columnSpanFull()
                                        ->required()
                                        ->prefixIcon('heroicon-s-at-symbol')
                                        ->unique(User::class, 'email')
                                        ->maxLength(255),

                                    TextInput::make('representative_password')
                                        ->label('Mot de passe')
                                        ->prefixIcon('heroicon-s-lock-closed')
                                        ->columnSpanFull()
                                        ->password()
                                        ->required()
                                        ->minLength(8),
                                ])
                                ->columns(2)
                                ->visible(fn (Get $get) => $get('account_type') === 'company'),
                        ]),
                ])
        ];
    }

    public function submit(FetchSiretInformation $fetchSiret)
    {
        $this->protectAgainstSpam(); // if is spam, will abort the request

        $data = $this->form->getState();

        /*
        |--------------------------------------------------------------------------
        | Individual
        |--------------------------------------------------------------------------
        */

        $userService = new UserService();

        if ($data['account_type'] === 'individual') {
            $validated = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'tenant_id' => $this->tenant->id,
                'role' => Roles::Subscriber,
            ];

            $user = $userService->storeUser(data: $validated, isRegister: true);

            Notification::make()
                ->success()
                ->title('Compte créé avec succès')
                ->body('Votre compte particulier a été créé.')
                ->send();

            \Auth::login($user);

            return redirect()->route('tenant.dashboard', ['tenant' => $this->tenant]);
        }

        /*
        |--------------------------------------------------------------------------
        | Organization
        |--------------------------------------------------------------------------
        */

        $legalInfo = $fetchSiret->execute($data['company_registration']);

        if (!$legalInfo) {
            Notification::make()
                ->danger()
                ->title('Erreur')
                ->body('SIRET introuvable')
                ->send();

            return;
        }

        $validated = array_merge([
            'name' => $data['company_name'],
            'tenant_id' => $this->tenant->id,
            'legal_siren' => $data['company_registration'] ?? null,
            'created_by' => 'anonymous'
        ], $legalInfo);

        $organization = Organization::create($validated);

        $validated = [
            'first_name' => $data['representative_first_name'],
            'last_name' => $data['representative_last_name'],
            'email' => $data['representative_email'],
            'password' => Hash::make($data['representative_password']),
            'tenant_id' => $this->tenant->id,
            'organization_id' => $organization->id,
            'role' => Roles::Subscriber,
        ];

        $user = $userService->storeUser(data: $validated, isRegister: true);

        $organization->update([
            'created_by' => $user->id
        ]);

        Notification::make()
            ->success()
            ->title('Compte entreprise créé avec succès')
            ->body('Votre compte entreprise a été créé.')
            ->send();

        \Auth::login($user);

        return redirect()->route('tenant.dashboard', ['tenant' => $this->tenant]);
    }

    public function render()
    {
        return view('livewire.forms.auth.register-form');
    }
}
