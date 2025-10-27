<?php

namespace App\Http\Livewire\Forms\Tenants;

use App\Models\Organization;
use App\Models\Tenant;
use App\Services\Features\Tco2Service;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class DetailsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Tenant $tenant;

    public function mount()
    {
        $this->form->fill([
            'tenant' => $this->tenant->toArray(),
            'logo' => str_replace('/storage/', '', $this->tenant->logo ?? ''),
            'logo_white' => str_replace('/storage/', '', $this->tenant->logo_white ?? ''),
            'login_image' => str_replace('/storage/', '', $this->tenant->login_image ?? ''),
            'email_banner' => str_replace('/storage/', '', $this->tenant->email_banner ?? ''),
            'contributor_space_banner_picture' => str_replace('/storage/', '', $this->tenant->contributor_space_banner_picture ?? ''),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [

            Tabs::make('')
                ->persistTabInQueryString()
                ->tabs([

                    Tabs\Tab::make('settings')
                        ->columns(2)
                        ->label('Paramètres')
                        ->schema([
                            Fieldset::make('name')
                                ->label('Dénomination & accès')
                                ->schema([
                                    TextInput::make('tenant.name')
                                        ->label("Nom de l'instance locale")
                                        ->lazy()
                                        ->required()
                                        ->autofocus(),
                                    TextInput::make('tenant.public_url')
                                        ->label('URL public')
                                        ->lazy()
                                        ->required()
                                        ->placeholder('https://')
                                        ->url(),
                                    TextInput::make('tenant.domain')
                                        ->label('Sous-domaine')
                                        ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                            $set('tenant.domain', Str::slug($state));
                                        })
                                        ->suffix('.'.config('app.displayed_url')),
                                ]),
                        ]),

                    Tabs\Tab::make('local')
                        ->columns(2)
                        ->label('Informations métier')
                        ->schema([
                            Fieldset::make('goal')
                                ->label('Coûts liés aux projets et financements')
                                ->schema([
                                    TextInput::make('tenant.default_commission')
                                        ->label('Commission par défaut (%)')
                                        ->placeholder('5')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->step(.01),

                                    TextInput::make('tenant.price_tco2')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->step(.01)
                                        ->suffix('€ HT / tonne')
                                        ->helperText('Tous les projets synchronisés seront mis à jour')
                                        ->label('Prix tonne CO2 (HT)')
                                        ->lazy(),
                                ]),

                            Select::make('tenant.default_organization_id')
                                ->label('Organisation par défaut')
                                ->required()
                                ->helperText('Cette organisation sera affiliée par défaut auc nouveaux projets.')
                                ->options(
                                    Organization::where('tenant_id', $this->tenant->id)->get()->pluck('name', 'id')
                                ),
                        ]),

                    Tabs\Tab::make('brand')
                        ->columns(2)
                        ->label('Image de marque')
                        ->schema([
                            FileUpload::make('logo')
                                ->label('Logo')
                                ->visibility('public')
                                ->preserveFilenames()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->image()
                                ->lazy(),

                            FileUpload::make('logo_white')
                                ->label('Logo thème sombre')
                                ->visibility('public')
                                ->preserveFilenames()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->image()
                                ->lazy(),

                            FileUpload::make('login_image')
                                ->label('Page de connexion')
                                ->visibility('public')
                                ->preserveFilenames()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->image()
                                ->lazy(),

                            Fieldset::make('interfaces_infos')
                                ->label('Emails')
                                ->schema([

                                    TextInput::make('tenant.sender_email')
                                        ->label('Email expéditeur')
                                        ->email()
                                        ->required(),

                                    TextInput::make('tenant.noreply_sender_email')
                                        ->label('Email expéditeur (noreply)')
                                        ->email()
                                        ->required(),

                                    FileUpload::make('email_banner')
                                        ->label('Bannière')
                                        ->visibility('public')
                                        ->columnSpanFull()
                                        ->helperText(
                                            new HtmlString("La bannière email doit mesurer 1200x400px. Exemple : <a download href='".asset('img/emails/header.png')."' class='hover:underline'>Bannière nationale</a> ")
                                        )
                                        ->preserveFilenames()
                                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                                            return formatFileName($file->getClientOriginalName());
                                        })
                                        ->image()
                                        ->lazy(),

                                    TextInput::make('tenant.support_email')
                                        ->label('Email du support')
                                        ->helperText("Affiché dans le footer de l'espace contributeur")
                                        ->email()
                                        ->required(),

                                    TextInput::make('tenant.dpo_email')
                                        ->label('Email du DPO')
                                        ->helperText("Affiché dans la page RGPD de l'espace contributeur")
                                        ->email()
                                        ->required(),
                                ]),
                        ]),

                    Tabs\Tab::make('payments')
                        ->columns(2)
                        ->label('Paiements')
                        ->schema([
                            Fieldset::make('payment_infos')
                                ->label('Payzen')
                                ->schema([
                                    TextInput::make('tenant.payzen_user_id')
                                        ->columnSpanFull()
                                        ->required()
                                        ->label('ID utilisateur Payzen'),

                                    TextInput::make('tenant.payzen_password_test')
                                        ->required()
                                        ->label('Payzen : Mot de passe TEST'),

                                    TextInput::make('tenant.payzen_password_prod')
                                        ->required()
                                        ->label('Payzen : Mot de passe PRODUCTION'),

                                    TextInput::make('tenant.auth_terminal_token')
                                        ->required()
                                        ->label('Borne : Token de connexion'),

                                    Toggle::make('tenant.payments_mode_test')
                                        ->onColor('success')
                                        ->inline(false)
                                        ->label('Mode test activé'),

                                ]),
                        ]),

                    Tabs\Tab::make('legals')
                        ->columns(2)
                        ->label('Informations légales')
                        ->schema([
                            TextInput::make('tenant.siret')
                                ->required()
                                ->label('N° SIRET'),

                            TextInput::make('tenant.vat_number')
                                ->required()
                                ->label('N° de TVA'),

                            TextInput::make('tenant.cgu')
                                ->required()
                                ->url()
                                ->label('Lien CGU'),

                            Fieldset::make('')
                                ->label('CGV')
                                ->schema([
                                    TextInput::make('tenant.cgv')
                                        ->required()
                                        ->url()
                                        ->label('Lien CGV'),

                                    DatePicker::make('tenant.cgv_updated_at')
                                        ->required()
                                        ->label('Mise à jour'),
                                ]),

                            TextInput::make('tenant.data_policy_url')
                                ->required()
                                ->url()
                                ->label('Lien politique de confidentialité'),

                            TextInput::make('tenant.phone')
                                ->label('Téléphone de contact'),

                            TextInput::make('tenant.address_1')
                                ->label('Adresse 1')
                                ->columnSpanFull()
                                ->lazy()
                                ->autocomplete(Str::orderedUuid())
                                ->autofocus(),

                            TextInput::make('tenant.postal_code')
                                ->label('Code postal')
                                ->lazy()
                                ->autocomplete(Str::orderedUuid()),

                            TextInput::make('tenant.city')
                                ->label('Ville')
                                ->lazy()
                                ->autocomplete(Str::orderedUuid()),
                        ]),

                    Tabs\Tab::make('contributor_space')
                        ->columns(2)
                        ->label('Interface contributeur')
                        ->schema([
                            Section::make('Bannière principale')
                                ->collapsible()
                                ->collapsed()
                                ->compact()
                                ->columns(1)
                                ->schema([

                                    Toggle::make('tenant.contributor_space_banner_activated')
                                        ->label("Bannière activée sur la page d'accueil"),

                                    TextInput::make('tenant.contributor_space_banner_title')
                                        ->label('Titre de la bannière'),

                                    RichEditor::make('tenant.contributor_space_banner_description')
                                        ->disableToolbarButtons(['attachFiles', 'codeBlock', 'h2', 'h3', 'link', 'attachFiles', 'blockquote', 'strike', 'bulletList', 'orderedList'])
                                        ->label('Description de la bannière'),

                                    Fieldset::make('Bouton')
                                        ->schema([

                                            TextInput::make('tenant.contributor_space_banner_button_text')
                                                ->label('Texte du bouton'),

                                            TextInput::make('tenant.contributor_space_banner_button_url')
                                                ->url()
                                                ->label('Texte du bouton'),

                                        ]),

                                    FileUpload::make('contributor_space_banner_picture')
                                        ->label('Arrière plan')
                                        ->visibility('public')
                                        ->openable()
                                        ->downloadable()
                                        ->columnSpanFull()
                                        ->preserveFilenames()
                                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                                            return formatFileName($file->getClientOriginalName());
                                        })
                                        ->image()
                                        ->imageEditor()
                                        ->lazy(),

                                ]),

                            Section::make('FAQ')
                                ->collapsible()
                                ->collapsed()
                                ->compact()
                                ->columns(1)
                                ->schema([

                                    Repeater::make('tenant.faq')
                                        ->label('Eléments de la FAQ')
                                        ->addActionLabel('Ajouter un élément dans la FAQ')
                                        ->schema([

                                            Textarea::make('question')
                                                ->label('Question')
                                                ->required(),

                                            Textarea::make('answer')
                                                ->label('Réponse')
                                                ->required(),
                                        ]),

                                ]),

                            Section::make('Ressources')
                                ->collapsible()
                                ->collapsed()
                                ->compact()
                                ->columns(1)
                                ->schema([

                                    Repeater::make('tenant.documents_communication')
                                        ->label('Documents pour communiquer sur leur contribution')
                                        ->columns(2)
                                        ->addActionLabel('Ajouter un document')
                                        ->schema([

                                            TextInput::make('title')
                                                ->required()
                                                ->label('Titre du document'),

                                            FileUpload::make('file')
                                                ->disk('public')
                                                ->openable()
                                                ->downloadable()
                                                ->preserveFilenames()
                                                ->required()
                                                ->label('Fichier'),
                                        ]),

                                    Repeater::make('tenant.external_resources')
                                        ->label('Ressources externes')
                                        ->addActionLabel('Ajouter une ressource')
                                        ->columns(2)
                                        ->schema([

                                            Select::make('type')
                                                ->label('Titre')
                                                ->reactive()
                                                ->options([
                                                    'link' => 'Lien externe',
                                                    'file' => 'Fichier',
                                                ]),

                                            TextInput::make('title')
                                                ->required()
                                                ->label('Titre de la ressource'),

                                            TextInput::make('link')
                                                ->columnSpanFull()
                                                ->visible(fn (Get $get): bool => $get('type') == 'link')
                                                ->required(fn (Get $get): bool => $get('type') == 'link')
                                                ->url()
                                                ->label('Lien'),

                                            FileUpload::make('file')
                                                ->disk('public')
                                                ->openable()
                                                ->downloadable()
                                                ->preserveFilenames()
                                                ->visible(fn (Get $get): bool => $get('type') == 'file')
                                                ->required(fn (Get $get): bool => $get('type') == 'file')
                                                ->label('Fichier'),
                                        ]),

                                ]),
                        ]),

                    Tabs\Tab::make('api')
                        ->columns(2)
                        ->label('API')
                        ->schema([

                            Placeholder::make('id')
                                ->label("ID de l'antenne locale")
                                ->content(
                                    new HtmlString("<span class='font-bold'>{$this->tenant->id}</span>")
                                ),

                            Fieldset::make('webhook')
                                ->label('Webhooks')
                                ->schema([
                                    TextInput::make('tenant.webhook_project_update')
                                        ->label('Webhook projets')
                                        ->url(),

                                    TextInput::make('tenant.webhook_users_update')
                                        ->label('Webhook utilisateurs')
                                        ->url(),

                                    TextInput::make('tenant.webhook_news_update')
                                        ->label('Webhook actualités')
                                        ->url(),
                                ]),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        $toBeUpdated = $this->form->getState();

        $toBeUpdated['logo'] = '/storage/'.$toBeUpdated['logo'];
        $toBeUpdated['logo_white'] = '/storage/'.$toBeUpdated['logo_white'];
        $toBeUpdated['login_image'] = $toBeUpdated['login_image'] ? '/storage/'.$toBeUpdated['login_image'] : null;
        $toBeUpdated['email_banner'] = '/storage/'.$toBeUpdated['email_banner'];
        $toBeUpdated['contributor_space_banner_picture'] = '/storage/'.$toBeUpdated['contributor_space_banner_picture'];

        if ($toBeUpdated['tenant']['price_tco2'] != $this->tenant->price_tco2) {
            $tco2Service = new Tco2Service();
            $tco2Service->newTenantPricePerTon(tenant: $this->tenant, newPrice: $toBeUpdated['tenant']['price_tco2']);
        }

        $this->tenant->update([
            ...$toBeUpdated['tenant'],
            'logo' => $toBeUpdated['logo'],
            'login_image' => $toBeUpdated['login_image'],
            'email_banner' => $toBeUpdated['email_banner'],
            'contributor_space_banner_picture' => $toBeUpdated['contributor_space_banner_picture'],
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Mise à jour effectuée')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.forms.tenants.details-form');
    }
}
