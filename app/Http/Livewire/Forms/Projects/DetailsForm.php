<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Enums\Roles;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;
use App\Models\MethodForm;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Models\Segmentation;
use App\Models\Certification;
use App\Helpers\ActivityHelper;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Http;
use App\Traits\Filament\HasDataState;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Actions\Contracts\HasActions;
use App\Enums\Models\Projects\ProjectStateEnum;
use App\Services\Features\AddressLookupService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class DetailsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Project $project;

    public array $tenants = [];

    public array $segmentations = [];

    public array $usersSponsor = [];

    public array $organizationSponsor = [];

    protected $cities_options = [];

    public function mount()
    {
        $this->tenants = Tenant::select(['id', 'name'])
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->organizationSponsor = Organization::select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->usersSponsor = User::select(['id', 'first_name', 'last_name', 'tenant_id'])
            ->tenantable()
            ->orderBy('last_name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->segmentations = Segmentation::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->project->loadMissing([
            'parentProject',
        ]);

        $this->form->fill([
            'project' => $this->project->toArray(),
            'thumbnail' => str_replace('/storage/', '', $this->project->thumbnail ?? ''),
            'featured_image' => str_replace('/storage/', '', $this->project->featured_image ?? ''),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->disabled(! request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
                ->schema([
                    Fieldset::make('name')
                        ->label('Dénomination & explications')
                        ->schema([
                            TextInput::make('project.name')
                                ->label('Nom du projet')
                                ->required()
                                ->autofocus(),

                            Select::make('project.state')
                                ->formatStateUsing(fn ($state): string => $state)
                                ->required()
                                ->searchable()
                                ->label('Statut')
                                ->selectablePlaceholder(false)
                                ->options(ProjectStateEnum::toArray()),

                            Textarea::make('project.summary')
                                ->label('Résumé')
                                ->rows(5)
                                ->columnSpanFull(),

                            RichEditor::make('project.description')
                                ->helperText('Fournissez une description concrète des activités des actions, etc.')
                                ->label('Description du projet')
                                ->toolbarButtons([
                                    'h2',
                                    'h3',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'undo',
                                ])
                                ->columnSpanFull(),

                            TextInput::make('project.dreal_reference')
                                ->readOnly(function () {
                                    if ($this->project->hasParent()) {
                                        return true;
                                    }

                                    return false;
                                })
                                ->placeholder(function () {
                                    if ($this->project->hasParent()) {
                                        return $this->project->parentProject->dreal_reference;
                                    }

                                    return null;
                                })
                                ->label('Référence DREAL')
                                ->nullable(),
                        ]),

                    Fieldset::make('project_management')
                        ->label('Visibilité / synchronisation projet')
                        ->schema([
                            Toggle::make('project.can_be_displayed_on_website')
                                ->inline(false)
                                ->live()
                                ->disabled(! $this->project->hasFundingAdded())
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    $initialValue = $get('project.can_be_financed_online');

                                    $set('project.can_be_financed_online', $state);

                                    Notification::make()
                                        ->title("Nous avons automatiquement mis à jour le statut 'Finançable en ligne'")
                                        ->body("Si vous souhaitez que ce projet ne soit pas finançable en ligne, désactivez l'option dans le formulaire.")
                                        ->info()
                                        ->send();
                                })
                                ->helperText(! $this->project->hasFundingAdded() ? 'Vous devez ajouter les informations de financement pour afficher le projet sur le site' : "Le projet sera visible sur le site public de l'instance locale.")
                                ->label('Affiché sur le site web'),

                            Toggle::make('project.can_be_financed_online')
                                ->inline(false)
                                ->helperText('Les visiteurs pourront ajouter des contributions directes sur ce projet.')
                                ->label('Finançable en ligne'),

                            Toggle::make('project.can_be_displayed_percentage_of_funding')
                                ->inline(false)
                                ->helperText('Le pourcentage de progression sera visible sur la page publique du projet.')
                                ->label("Affichage du pourcentage d'avancement"),

                            Toggle::make('project.can_be_displayed_on_terminal')
                                ->inline(false)
                                ->hidden($this->project->hasParent())
                                ->helperText('Le projet sera visible et finançable sur la borne.')
                                ->label('Affichage sur borne'),

                            Toggle::make('project.is_funded')
                                ->inline(false)
                                ->hidden($this->project->hasParent())
                                ->helperText("S'il est financé, il ne sera pas affiché sur la borne.")
                                ->label('Le projet est financé ?'),
                        ]),

                    Fieldset::make('address')
                        ->label('Adresse du projet')
                        ->hidden($this->project->hasParent() && ! is_null($this->project->sub_project_year))
                        ->schema([
                            TextInput::make('project.address_1')
                                ->label('Adresse 1')
                                ->lazy()
                                ->autocomplete(Str::orderedUuid())
                                ->placeholder('Forêt de ...')
                                ->autofocus(),
                            TextInput::make('project.address_2')
                                ->label('Complément')
                                ->lazy()
                                ->placeholder('xx chemin de la forêt'),

                            TextInput::make('project.address_postal_code')
                                ->label('Code postal')
                                ->lazy()
                                ->required(! $this->project->hasParent())
                                ->autocomplete(Str::orderedUuid())
                                ->placeholder('17000')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Set $set, Get $get, AddressLookupService $addressService) {
                                    $cities = $addressService->findCitiesByPostalCode($state);

                                    $set('project.cities_options', $cities);
                                    $currentCity = $get('project.address_city');

                                    if (!empty($cities)) {
                                        if (empty($currentCity) || !array_key_exists($currentCity, $cities)) {
                                            $set('project.address_city', array_key_first($cities));
                                        }
                                    } else {
                                        $set('project.address_city', null);
                                    }

                                    if (!empty($cities)) {
                                        Notification::make()
                                            ->title('Liste des villes mises à jour dans le sélecteur.')
                                            ->info()
                                            ->send();
                                    }
                                }),

                            Select::make('project.address_city')
                                ->label('Ville')
                                ->required(! $this->project->hasParent())
                                ->options(fn (Get $get) => $get('project.cities_options') ?? [])
                                ->searchable()
                                ->reactive()
                                ->placeholder('Lyon')
                                ->preload(),

                        ]),

                    Fieldset::make('informations')
                        ->label('Certification & instance locale')
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema([
                            Select::make('project.tenant_id')
                                ->label('Instance locale')
                                ->searchable()
                                ->placeholder('Projet national')
                                ->disabled($this->project->hasParent())
                                ->helperText($this->project->hasParent() ? 'Vous pouvez modifier cette information sur le projet parent.' : null)
                                ->options($this->tenants),

                            Select::make('project.certification_id')
                                ->label('Certification')
                                ->searchable()
                                ->disabled($this->project->hasParent())
                                ->helperText($this->project->hasParent() ? 'Vous pouvez modifier cette information sur le projet parent.' : null)
                                ->options(function (\Filament\Forms\Get $get) {
                                    return Certification::select(['id', 'name', 'tenant_id'])
                                        ->whereNull('tenant_id')
                                        ->orWhere('tenant_id', $get('project.tenant_id'))
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }),

                            Select::make('project.segmentation_id')
                                ->label('Segmentation')
                                ->disabled($this->project->hasParent())
                                ->required(! $this->project->hasParent())
                                ->helperText($this->project->hasParent() ? 'Vous pouvez modifier cette information sur le projet parent.' : null)
                                ->searchable()
                                ->options($this->segmentations),
                            Select::make('project.method_form_id')
                                ->label('Méthode')
                                ->searchable()
                                ->reactive()
                                ->hidden($this->project->hasParent())
                                ->disabled(! is_null($this->project->method_form_id))
                                ->helperText(! is_null($this->project->method_form_id) ? 'Vous avez déjà modifier au moins un élément de la méthode. Supprimer la méthode pour la modifier.' : 'La liste des méthodes dépend de la segmentation sélectionnée.')
                                ->options(function (\Filament\Forms\Get $get) {
                                    // Récupérer d'abord les IDs des méthodes actives depuis method_form_groups
                                    $activeMethodFormIds = \App\Models\MethodFormGroup::where('segmentation_id', $get('project.segmentation_id'))
                                        ->pluck('active_method_form_id')
                                        ->toArray();
                                    
                                    // Inclure toujours l'ID de la méthode actuelle si elle existe
                                    if (!is_null($this->project->method_form_id)) {
                                        $activeMethodFormIds[] = $this->project->method_form_id;
                                    }
                                    // Récupérer les méthodes dont l'ID est dans $activeMethodFormIds
                                    return \App\Models\MethodForm::whereIn('id', $activeMethodFormIds)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                
                                
                                }),
                        ]),

                    Fieldset::make('illustrations')
                        ->label('Illustrations')
                        ->schema([
                            FileUpload::make('thumbnail')
                                ->label('Miniature')
                                ->visibility('public')
                                ->preserveFilenames()
                                ->maxWidth(1200)
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return Str::slug($this->project->name).'_thumbnail.'.$file->getClientOriginalExtension();
                                })
                                ->acceptedFileTypes([
                                    'image/*',
                                ])
                                ->lazy(),

                            FileUpload::make('featured_image')
                                ->label('Image mise en avant')
                                ->visibility('public')
                                ->preserveFilenames()
                                ->maxWidth(1200)
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return Str::slug($this->project->name).'_featured_image.'.$file->getClientOriginalExtension();
                                })
                                ->acceptedFileTypes([
                                    'image/*',
                                ])
                                ->lazy(),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        $formState = $this->form->getState();
        $toBeUpdated = $formState['project'];

        // Manage thumbnail
        // $formState['thumbnail'] will contain the file name (relative to the public disk) if an image is present/uploaded,
        // or null if the image has been removed by the user or if the field was initially empty.
        if (array_key_exists('thumbnail', $formState)) {
            $toBeUpdated['thumbnail'] = $formState['thumbnail'] ? '/storage/' . $formState['thumbnail'] : null;
        }

        if (array_key_exists('featured_image', $formState)) {
            $toBeUpdated['featured_image'] = $formState['featured_image'] ? '/storage/' . $formState['featured_image'] : null;
        }

        // Trim string
        $toBeUpdated = collect($toBeUpdated)->map(function ($value) {
            if (is_string($value)) {
                return trim($value);
            }

            return $value;
        })->toArray();

        $this->project->update($toBeUpdated);

        if (\Arr::has($this->project->getChanges(), 'address_1')
            or \Arr::has($this->project->getChanges(), 'address_city')
                or \Arr::has($this->project->getChanges(), 'address_postal_code')
        ) {
            $requestAddress = Http::get('https://api-adresse.data.gouv.fr/search/?q='.urlencode(\Arr::join([$this->project->address_1, $this->project->address_city, $this->project->address_postal_code], ' ')));

            if ($requestAddress->ok() and \Arr::has($requestAddress->json(), 'features.0.geometry')) {
                $coordinates = \Arr::get($requestAddress->json(), 'features.0.geometry.coordinates');

                $this->project->update([
                    'lat' => $coordinates[1],
                    'lng' => $coordinates[0],
                ]);
            } else {
                Notification::make()
                    ->title('Adresse non-trouvée')
                    ->body("Nous n'avons pu trouver les coordonnées GPS pour cette adresse. La position GPS n'a donc pas été mise à jour.")
                    ->warning()
                    ->send();
            }
        }

        Notification::make()
            ->title('Les modifications ont été enregistrées')
            ->body('Pour les mettre à jour sur le site web, il faut cliquer sur le bouton "synchroniser" sur la page générale des projets.')
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->project,
            title: 'Mise à jour des informations du projet',
            url: route('projects.show.details', ['project' => $this->project])
        );


    }


    public function render()
    {
        return view('livewire.forms.projects.details-form');
    }
}