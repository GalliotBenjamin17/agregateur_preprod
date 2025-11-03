<?php

namespace App\Http\Livewire\Forms\Settings;

use App\Models\MethodForm;
use App\Traits\Filament\HasDataState;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

class MethodFormForm extends Component implements HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }

    public MethodForm $methodForm;

    public array $availableQuestions = [];

    public function mount()
    {
        $this->form->fill([
            'methodForm' => $this->methodForm,
            'skeleton' => $this->methodForm->skeleton,
        ]);
        $this->updateAvailableQuestions();
    }

    public function updateAvailableQuestions(): void
    {
        $this->availableQuestions = collect($this->data['skeleton'])
            ->flatMap(function ($section) {
                return collect($section['content'])->mapWithKeys(function ($question) {
                    return [
                        $question['data']['id'] => $question['data']['label'],
                    ];
                });
            })
            ->toArray();
    }

    protected function findQuestionById(?string $id): ?array
    {
        foreach ($this->data['skeleton'] as $section) {
            foreach ($section['content'] as $question) {
                if ($question['data']['id'] === $id) {
                    return $question;
                }
            }
        }

        return null;
    }

    protected function getConditionalOperator(): Select
    {
        return Select::make('condition_operator')
            ->label('Opérateur')
            ->options(function (Get $get) {
                $questionId = $get('condition_question');
                $question = $this->findQuestionById($questionId);

                if (! $question) {
                    return [];
                }

                switch ($question['type']) {
                    case 'input':
                    case 'rich_text':
                        return [
                            'contains' => 'Contient',
                            'not_contains' => 'Ne contient pas',
                            'equals' => 'Égal à',
                            'not_equals' => 'Différent de',
                        ];
                    case 'input_numeric':
                        return [
                            'equals' => 'Égal à',
                            'not_equals' => 'Différent de',
                            'greater_than' => 'Supérieur à',
                            'less_than' => 'Inférieur à',
                        ];
                    case 'date':
                        return [
                            'before' => 'Avant',
                            'after' => 'Après',
                            'on' => 'Cette date',
                        ];
                    case 'checkbox':
                        return [
                            'is_checked' => 'Est coché',
                            'is_not_checked' => 'N\'est pas coché',
                        ];
                    case 'select':
                    case 'multiple_choices':
                        return [
                            'equals' => 'Égal à',
                            'not_equals' => 'Différent de',
                            'contains' => 'Contient',
                        ];
                    case 'file':
                        return [
                            'has_file' => 'A uploadé un fichier',
                            'has_no_file' => 'N\'a pas uploadé de fichier',
                        ];
                    default:
                        return [];
                }
            })
            ->visible(fn (Get $get) => $get('condition_question'))
            ->reactive()
            ->afterStateUpdated(fn (Set $set) => $set('condition_value', null));
    }

    public function isVisibleInRepeater(Get $get): bool
    {
        $question = $this->findQuestionById($get('condition_question'));
        $operator = $get('condition_operator');

        if (! $question) {
            return false;
        }

        return ! in_array($question['type'], ['checkbox', 'file']) &&
            ! in_array($operator, ['is_checked', 'is_not_checked', 'has_file', 'has_no_file']);
    }

    protected function getConditionalValueField(Get $get)
    {
        return $this->getAdaptedComponent(get: $get)
            ->visible(function (callable $get) {
                $questionId = $get('condition_question');
                $question = $this->findQuestionById($questionId);
                $operator = $get('condition_operator');

                if (! $question || ! $operator || ! $get('has_condition')) {
                    return false;
                }

                return ! in_array($question['type'], ['checkbox', 'file']) &&
                    ! in_array($operator, ['is_checked', 'is_not_checked', 'has_file', 'has_no_file']);
            })
            ->dehydrated(fn (callable $get) => $get('has_condition'))
            ->reactive();
    }

    protected function getAdaptedComponent(Get $get)
    {
        $questionId = $get('condition_question');
        $question = $this->findQuestionById($questionId);

        if (! $question) {
            return TextInput::make('condition_value')->label('Valeur');
        }

        return match ($question['type']) {
            'input', 'rich_text', 'input_numeric' => TextInput::make('condition_value')
                ->label('Valeur'),
            'date' => DatePicker::make('condition_value')
                ->label('Valeur'),
            'select', 'multiple_choices' => Select::make('condition_value')
                ->label('Valeur')
                ->options(collect($question['data']['choices'])->mapWithKeys(fn ($choice, $key) => [$key => $choice])->toArray())
                ->searchable(),
            default => TextInput::make('condition_value')
                ->label('Valeur'),
        };
    }

    public function getVisibilitySelector(?string $title = 'Visibilité de la question')
    {
        return Section::make($title)
            ->compact()
            ->collapsed()
            ->columns(3)
            ->collapsible()
            ->schema(function (Get $get) {
                return [
                    Toggle::make('has_condition')
                        ->columnSpanFull()
                        ->label('Visibilité conditionnelle')
                        ->reactive(),

                    Select::make('condition_question')
                        ->label('Question conditionnelle')
                        ->options(function (Get $get) {
                            return collect($this->availableQuestions)->filter(fn ($value, $key) => $get('id') != $key);
                        })
                        ->visible(fn (Get $get) => $get('has_condition'))
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $set('condition_operator', null);
                            $set('condition_value', null);
                        }),

                    $this->getConditionalOperator(),
                    $this->getConditionalValueField($get),
                ];
            });
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('information')
                ->label('Informations')
                ->columnSpan(1)
                ->schema([
                    TextInput::make('methodForm.name')
                        ->label('Nom de la version')
                        ->lazy()
                        ->required()
                        ->disabled(! is_null($this->methodForm->locked_at))
                        ->columnSpanFull()
                        ->autofocus(),
                ]),

            Repeater::make('skeleton')
                ->columnSpan(1)
                ->itemLabel(fn (array $state): ?string => $state['section_title'] ?? null)
                ->cloneable(is_null($this->methodForm->locked_at))
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->addActionLabel('Ajouter une section')
                ->addable()
                ->deletable()
                ->reorderable()
                ->schema([
                    TextInput::make('section_id')
                        ->required()
                        ->hidden()
                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                            if (! $state) {
                                $component->state(Str::orderedUuid()->toString());
                            }
                        }),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('section_title')
                                ->required()
                                ->disabled(! is_null($this->methodForm->locked_at))
                                ->label('Nom de la section'),

                            Select::make('certification_state')
                                ->options(config('values.states.certification.name'))
                                ->required()
                                ->searchable()
                                ->disabled(! is_null($this->methodForm->locked_at))
                                ->selectablePlaceholder(false)
                                ->label('Étape de la certification'),

                            Textarea::make('description')
                                ->columnSpanFull()
                                ->rows(2)
                                ->placeholder('Cette description sera affichée en haut de la section.')
                                ->disabled(! is_null($this->methodForm->locked_at))
                                ->label('Description'),
                        ]),

                    Builder::make('content')
                        ->label('Contenu de la section')
                        ->collapsible()
                        ->cloneable(is_null($this->methodForm->locked_at))
                        ->addActionLabel('Ajouter un champ à la section')
                        ->addable()
                        ->deletable()
                        ->reorderable()
                        ->blocks([
                            Builder\Block::make('input')
                                ->label(fn ($state): ?string => Str::limit($state['label'] ?? 'Champ texte', 50))
                                ->icon('heroicon-o-pencil-square')
                                ->columns()
                                ->schema([
                                    TextInput::make('id')
                                        ->required()
                                        ->hidden()
                                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                            if (! $state) {
                                                $component->state(Str::orderedUuid()->toString());
                                            }
                                        }),

                                    TextInput::make('label')
                                        ->required()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Label'),

                                    TextInput::make('description')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Description / Aide'),

                                    Toggle::make('required')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->inline(false)
                                        ->label('Réponse obligatoire'),

                                    $this->getVisibilitySelector('Information de visibilité'),

                                ])->columns(3),

                            Builder\Block::make('input_numeric')
                                ->label(fn ($state): ?string => Str::limit($state['label'] ?? 'Champ numeric', 50))
                                ->icon('heroicon-o-hashtag')
                                ->columns()
                                ->schema([
                                    TextInput::make('id')
                                        ->required()
                                        ->hidden()
                                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                            if (! $state) {
                                                $component->state(Str::orderedUuid()->toString());
                                            }
                                        }),

                                    TextInput::make('label')
                                        ->required()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Label'),

                                    TextInput::make('description')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Description / Aide'),

                                    Toggle::make('required')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->inline(false)
                                        ->label('Réponse obligatoire'),

                                    $this->getVisibilitySelector('Information de visibilité'),

                                ])->columns(3),

                            Builder\Block::make('date')
                                ->label(fn ($state): ?string => Str::limit($state['label'] ?? 'Champ date', 50))
                                ->icon('heroicon-o-calendar')
                                ->columns()
                                ->schema([
                                    TextInput::make('id')
                                        ->required()
                                        ->hidden()
                                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                            if (! $state) {
                                                $component->state(Str::orderedUuid()->toString());
                                            }
                                        }),

                                    TextInput::make('label')
                                        ->required()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Label'),

                                    TextInput::make('description')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Description / Aide'),

                                    Toggle::make('required')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->inline(false)
                                        ->label('Réponse obligatoire'),

                                    $this->getVisibilitySelector('Information de visibilité'),

                                ])->columns(3),

                            Builder\Block::make('checkbox')
                                ->label(fn ($state): ?string => Str::limit($state['label'] ?? 'Checkbox', 50))
                                ->icon('heroicon-o-check')
                                ->columns()
                                ->schema([
                                    TextInput::make('id')
                                        ->required()
                                        ->hidden()
                                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                            if (! $state) {
                                                $component->state(Str::orderedUuid()->toString());
                                            }
                                        }),

                                    TextInput::make('label')
                                        ->required()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Label'),
                                    TextInput::make('description')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Description / Aide'),
                                    Toggle::make('required')
                                        ->inline(false)
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Réponse obligatoire'),

                                    $this->getVisibilitySelector('Information de visibilité'),

                                ])->columns(3),

                            Builder\Block::make('select')
                                ->label(fn ($state): ?string => Str::limit($state['label'] ?? 'Sélecteur', 50))
                                ->icon('heroicon-o-document-text')
                                ->columns()
                                ->schema([
                                    TextInput::make('id')
                                        ->required()
                                        ->hidden()
                                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                            if (! $state) {
                                                $component->state(Str::orderedUuid()->toString());
                                            }
                                        }),

                                    TextInput::make('label')
                                        ->required()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Label'),
                                    TextInput::make('description')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Description / Aide'),
                                    Toggle::make('required')
                                        ->inline(false)
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Réponse obligatoire'),
                                    Toggle::make('multiple')
                                        ->inline(false)
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Choix multiples'),
                                    KeyValue::make('choices')
                                        ->label('Choix')
                                        ->columnSpanFull()
                                        ->reorderable()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->rules([
                                            function () {
                                                return function (string $attribute, $value, \Closure $fail) {
                                                    if (is_array($value)) {
                                                        $values = array_values($value);

                                                        foreach ($values as $key => $subValue) {
                                                            if (! $subValue or $subValue == '') {
                                                                $fail('Vous devez saisir une valeur pour chacune des options.');
                                                            }
                                                        }
                                                    }
                                                };
                                            },
                                        ])
                                        ->keyLabel('ID')
                                        ->valueLabel('Valeur'),

                                    $this->getVisibilitySelector('Information de visibilité'),

                                ])->columns(4),

                            Builder\Block::make('richtext')
                                ->label(fn ($state): ?string => Str::limit($state['label'] ?? 'Text enrichi', 50))
                                ->icon('heroicon-o-pencil-square')
                                ->columns()
                                ->schema([
                                    TextInput::make('id')
                                        ->required()
                                        ->hidden()
                                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                            if (! $state) {
                                                $component->state(Str::orderedUuid()->toString());
                                            }
                                        }),

                                    TextInput::make('label')
                                        ->required()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Label'),

                                    TextInput::make('description')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Description / Aide'),

                                    Toggle::make('required')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->inline(false)
                                        ->label('Réponse obligatoire'),

                                    $this->getVisibilitySelector('Information de visibilité'),

                                ])->columns(3),

                            Builder\Block::make('file')
                                ->label(fn ($state): ?string => Str::limit($state['label'] ?? 'Fichiers', 50))
                                ->icon('heroicon-o-folder-plus')
                                ->columns()
                                ->schema([
                                    TextInput::make('id')
                                        ->required()
                                        ->hidden()
                                        ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                            if (! $state) {
                                                $component->state(Str::orderedUuid()->toString());
                                            }
                                        }),

                                    TextInput::make('label')
                                        ->required()
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Label'),
                                    TextInput::make('description')
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Description / Aide'),
                                    Toggle::make('required')
                                        ->inline(false)
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Réponse obligatoire'),
                                    Toggle::make('multiple')
                                        ->inline(false)
                                        ->disabled(! is_null($this->methodForm->locked_at))
                                        ->label('Plusieurs fichiers autorisés'),

                                    $this->getVisibilitySelector('Information de visibilité'),

                                ])->columns(4),
                        ]),
                ]),
        ];
    }

    public function lockMethodForm()
    {
        $this->submit();

        $this->methodForm->update([
            'locked_at' => now(),
            'locked_by' => request()->user()->id,
        ]);

        \Session::flash('success', 'La méthode est maintenant bloquée et ne peut plus être modifiée.');

        return to_route('settings.method-form-groups.method-form.show', ['methodFormGroup' => $this->methodForm->methodFormGroup->slug, 'methodForm' => $this->methodForm->id]);
    }

    public function preventDuplicatesIds(array $skeleton)
    {
        $ids = [];

        foreach ($skeleton as &$section) {
            // Ensure section_id exists; generate if missing or duplicated
            $currentSectionId = $section['section_id'] ?? null;
            if (! $currentSectionId || in_array($currentSectionId, $ids, true)) {
                $currentSectionId = Str::orderedUuid()->toString();
                $section['section_id'] = $currentSectionId;
            }

            // Ensure content is an array to iterate safely
            $content = $section['content'] ?? [];
            if (! is_array($content)) {
                $content = [];
            }

            foreach ($content as $index => $field) {
                // Ensure nested keys exist
                $fieldData = $field['data'] ?? [];
                $fieldId = $fieldData['id'] ?? null;

                if (! $fieldId || in_array($fieldId, $ids, true)) {
                    $fieldId = Str::orderedUuid()->toString();
                    $fieldData['id'] = $fieldId;
                }

                // Persist potential fixes back to section content
                $field['data'] = $fieldData;
                $section['content'][$index] = $field;

                $ids[] = $fieldId;
            }

            $ids[] = $currentSectionId;
        }

        return $skeleton;
    }

    public function submit()
    {
        $state = $this->form->getState();

        $skeleton = $this->preventDuplicatesIds($state['skeleton']);

        $this->methodForm->update([
            'name' => $state['methodForm']['name'],
            'skeleton' => $skeleton,
        ]);

        Notification::make()
            ->title('Formulaire mis à jour.')
            ->body("Formulaire mis à jour. Vous pourrez l'attitrer à un projet une fois celui-ci bloqué.")
            ->success()
            ->send();

        $this->dispatch('schemaUpdated');

        Notification::make()
            ->title('Prévisualisation mise à jour.')
            ->body('Vous pouvez maintenant prévisualiser le formulaire.')
            ->actions([
                Action::make('view')
                    ->label('Ouvrir la prévisualisation')
                    ->extraAttributes(['data-bs-toggle' => 'modal', 'data-bs-target' => '#preview'])
                    ->button(),
            ])
            ->success()
            ->send();

    }

    public function render()
    {
        return view('livewire.forms.settings.method-form-form');
    }
}
