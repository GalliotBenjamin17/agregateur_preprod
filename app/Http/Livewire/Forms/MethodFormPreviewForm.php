<?php

namespace App\Http\Livewire\Forms;

use App\Models\MethodForm;
use App\Traits\Filament\HasDataState;
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
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class MethodFormPreviewForm extends Component implements HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }

    public MethodForm $methodForm;

    protected $listeners = [
        'schemaUpdated',
    ];

    public function mount()
    {
        $this->form->fill([
            'data' => $this->methodForm->skeleton,
        ]);
    }

    public function schemaUpdated()
    {
        $this->methodForm->refresh();
        $this->render();
    }

    protected function getFormSchema(): array
    {
        return array_map(function (array $field) {
            return Fieldset::make(\Str::slug($field['section_title']))
                ->label(\Arr::get(config('values.states.certification.name'), $field['certification_state']).' // '.$field['section_title'])
                ->schema(function () use ($field) {
                    return array_merge([
                        Placeholder::make('description')
                            ->disableLabel()
                            ->visible(\Arr::has($field, 'description'))
                            ->columnSpanFull()
                            ->content(
                                new HtmlString("<span class='italic text-sm'>".Arr::get($field, 'description', '').'</span>')
                            ),
                    ], array_map(function ($row) {
                        return match ($row['type']) {
                            'input' => TextInput::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required']),

                            'input_numeric' => TextInput::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->numeric()
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required']),

                            'checkbox' => Toggle::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->inline(false)
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required']),

                            'date' => DatePicker::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required']),

                            'select' => Select::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->options($row['data']['choices'])
                                ->columnSpanFull()
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required']),

                            'richtext' => RichEditor::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
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
                                ->columnSpanFull()
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required']),

                            'file' => FileUpload::make('data.'.$row['data']['id'])
                                ->label($row['data']['label'])
                                ->visibility('public')
                                ->preserveFilenames()
                                ->columnSpanFull()
                                ->openable()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->downloadable()
                                ->multiple(true)
                                ->helperText($row['data']['description'])
                                ->required($row['data']['required']),
                        };
                    }, $field['content']));
                });
        }, $this->methodForm->skeleton ?? []);
    }

    public function render()
    {
        return view('livewire.forms.method-form-preview-form');
    }
}
