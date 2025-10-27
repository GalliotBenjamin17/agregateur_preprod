<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Enums\Roles;
use App\Models\MethodForm;
use App\Models\Project;
use App\States\Certification\Notified;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class MethodFormRepliesForm extends Component implements HasForms
{
    use InteractsWithForms;

    public MethodForm $methodForm;

    public Project $project;

    public ?array $data = [];

    public function mount()
    {
        $tempDatas = $this->project?->method_replies ?? [];
        $pushedDatas = [];

        foreach ($tempDatas['data'] ?? [] as $key => $value) {
            if (is_string($value) or is_bool($value) or is_null($value)) {
                $pushedDatas[$key] = $value;

                continue;
            }

            if (is_array($value)) {
                foreach ($value as $element) {
                    $pushedDatas[$key][] = Str::of($element)->replace('storage/', '')
                        ->replace('//', '/')
                        ->toString();
                }
            }
        }

        $this->data['data'] = $pushedDatas;
        $this->publishMissingFields();
    }

    public function publishMissingFields(): void
    {
        collect($this->methodForm->skeleton)->map(function ($section) {
            collect($section['content'])->map(function ($item) {
                if (! Arr::has($this->data, 'data.'.$item['data']['id'])) {
                    $this->data['data'][$item['data']['id']] = match ($item['type']) {
                        'checkbox' => false,
                        default => null
                    };
                }
            });
        });
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return array_map(function (array $field) {

            $checkVisibility = function (Get $get, array $row) {
                if (! isset($row['data']['has_condition']) || ! $row['data']['has_condition']) {
                    return true;
                }

                $questionValue = $get('data.'.$row['data']['condition_question']);
                $operator = $row['data']['condition_operator'];
                $conditionValue = $row['data']['condition_value'];

                return match ($operator) {
                    'contains' => str_contains($questionValue, $conditionValue),
                    'not_contains' => ! str_contains($questionValue, $conditionValue),
                    'equals' => $questionValue == $conditionValue,
                    'not_equals' => $questionValue != $conditionValue,
                    'greater_than' => $questionValue > $conditionValue,
                    'less_than' => $questionValue < $conditionValue,
                    'before' => strtotime($questionValue) < strtotime($conditionValue),
                    'after' => strtotime($questionValue) > strtotime($conditionValue),
                    'on' => $questionValue == $conditionValue,
                    'is_checked' => $questionValue === true,
                    'is_not_checked' => $questionValue === false,
                    'has_file' => ! empty($questionValue),
                    'has_no_file' => empty($questionValue),
                    default => true,
                };
            };

            return Fieldset::make(\Str::slug($field['section_title']))
                ->disabled(! request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
                ->visible(\Arr::get(config('values.states.certification.ranks'), $field['certification_state']) <= $this->project->certification_state->rank())
                ->label(\Arr::get(config('values.states.certification.name'), $field['certification_state']).' / '.$field['section_title'])
                ->schema(function () use ($field, $checkVisibility) {
                    return array_merge([
                        Placeholder::make('description')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->visible(Arr::has($field, 'description'))
                            ->content(
                                new HtmlString("<span class='font-semibold'>Information : </span><span class='italic'>".Arr::get($field, 'description', '').'</span>')
                            ),
                    ], array_map(function ($row) use ($checkVisibility) {
                        return match ($row['type']) {
                            'input' => TextInput::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->disabled($this->project->certification_state->rank() >= config('values.states.certification.ranks.verified'))
                                ->helperText($row['data']['description'])
                                ->visible(function (Get $get) use ($checkVisibility, $row) {
                                    return $checkVisibility($get, $row);
                                })
                                ->reactive()
                                ->required($row['data']['required']),

                            'input_numeric' => TextInput::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->numeric()
                                ->disabled($this->project->certification_state->rank() >= config('values.states.certification.ranks.verified'))
                                ->helperText($row['data']['description'])
                                ->visible(function (Get $get) use ($checkVisibility, $row) {
                                    return $checkVisibility($get, $row);
                                })
                                ->reactive()
                                ->required($row['data']['required']),

                            'checkbox' => Toggle::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->inline(false)
                                ->disabled($this->project->certification_state->rank() >= config('values.states.certification.ranks.verified'))
                                ->helperText($row['data']['description'])
                                ->visible(function (Get $get) use ($checkVisibility, $row) {
                                    return $checkVisibility($get, $row);
                                })
                                ->reactive()
                                ->required($row['data']['required']),

                            'date' => DatePicker::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->disabled($this->project->certification_state->rank() >= config('values.states.certification.ranks.verified'))
                                ->helperText($row['data']['description'])
                                ->displayFormat('d/m/Y')
                                ->visible(function (Get $get) use ($checkVisibility, $row) {
                                    return $checkVisibility($get, $row);
                                })
                                ->reactive()
                                ->required($row['data']['required']),

                            'select' => Select::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->disabled($this->project->certification_state->rank() >= config('values.states.certification.ranks.verified'))
                                ->options($row['data']['choices'])
                                ->multiple($row['data']['multiple'])
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required'])
                                ->visible(function (Get $get) use ($checkVisibility, $row) {
                                    return $checkVisibility($get, $row);
                                })
                                ->reactive(),

                            'richtext' => RichEditor::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->columnSpanFull()
                                ->disabled($this->project->certification_state->rank() >= config('values.states.certification.ranks.verified'))
                                ->helperText($row['data']['description'])
                                ->toolbarButtons([
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'underline',
                                    'undo',
                                ])
                                ->visible(function (Get $get) use ($checkVisibility, $row) {
                                    return $checkVisibility($get, $row);
                                })
                                ->reactive()
                                ->required($row['data']['required']),

                            'file' => FileUpload::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->visibility('public')
                                ->disabled($this->project->certification_state->rank() >= config('values.states.certification.ranks.verified'))
                                ->preserveFilenames()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->openable()
                                ->downloadable()
                                ->multiple(true)
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required'])
                                ->visible(function (Get $get) use ($checkVisibility, $row) {
                                    return $checkVisibility($get, $row);
                                })
                                ->reactive(),
                        };
                    }, $field['content']));
                });
        }, $this->methodForm->skeleton ?? []);
    }

    public function manageFiles($item)
    {
        if (is_null($item)) {
            return $item;
        }

        $elements = [];

        $keys = array_keys($item);

        foreach ($keys as $key) {
            $file = $item[$key];

            if (is_string($file)) {
                $elements[] = 'storage/'.$file;

                continue;
            }

            $path = Str::replace('public', '/storage', $file->storeAs('methods', $file->getClientOriginalName()));
            $elements[] = $path;
        }

        return $elements;
    }

    public function parseFiles()
    {
        $items = $this->data;

        array_map(function (array $field) use (&$items) {
            return array_map(function ($row) use (&$items) {
                if ($row['type'] == 'file' and \Arr::has($this->data['data'], $row['data']['id'])) {
                    $items['data'][$row['data']['id']] = $this->manageFiles($this->data['data'][$row['data']['id']]);
                }
            }, $field['content']);
        }, $this->methodForm->skeleton ?? []);

        return $items;
    }

    public function submit()
    {
        $items = $this->parseFiles();

        $this->project->update([
            'method_replies' => $items,
        ]);

        \Session::flash('success', 'Les informations ont été mises à jour.');

        return to_route('projects.show.methods-informations', ['project' => $this->project]);
    }

    public function resetMethodProject()
    {
        $this->project->update([
            'method_form_id' => null,
            'certification_state' => Notified::$name,
        ]);

        return to_route('projects.show.methods-informations', ['project' => $this->project]);
    }

    public function render()
    {
        return view('livewire.forms.projects.method-form-replies-form');
    }
}
