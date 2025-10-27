<?php

namespace App\Http\Livewire\Actions\News;

use App\Enums\Models\News\NewsStateEnum;
use App\Models\News;
use App\Models\Project;
use App\Services\Models\NewsService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class CreateForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Project $project = null;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajouter une actualité')
            ->size('sm')
            ->modalDescription('Vous pouvez créer une actualité avec le statut de brouillon pour la faire valider en amont de sa publication.')
            ->form([
                Grid::make()
                    ->schema([
                        Select::make('project_id')
                            ->label('Projet')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->required(is_null($this->project))
                            ->visible(is_null($this->project))
                            ->relationship('project', 'name', fn (Builder $query) => $query->when(userHasTenant(), function ($query) {
                                return $query->where('tenant_id', userTenantId());
                            })),

                        TextInput::make('name')
                            ->label('Titre')
                            ->columnSpanFull()
                            ->required(),

                        Select::make('state')
                            ->searchable()
                            ->label('Statut')
                            ->reactive()
                            ->required()
                            ->options(NewsStateEnum::toArray()),

                        DateTimePicker::make('scheduled_at')
                            ->label('Planifié à')
                            ->native()
                            ->seconds(false)
                            ->required(fn (Get $get) => $get('state') == NewsStateEnum::Scheduled->databaseKey())
                            ->visible(fn (Get $get) => $get('state') == NewsStateEnum::Scheduled->databaseKey()),

                        RichEditor::make('content')
                            ->columnSpanFull()
                            ->required()
                            ->disableToolbarButtons(['attachFiles', 'codeBlock', 'h2', 'h3', 'link', 'blockquote', 'strike'])
                            ->label('Contenu'),

                        Select::make('author_id')
                            ->label('Auteur')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                            ->relationship('author', 'first_name', fn (Builder $query) => $query->when(userHasTenant(), function ($query) {
                                return $query->where('tenant_id', userTenantId());
                            })),

                        Toggle::make('is_featured')
                            ->label('Mise en avant')
                            ->helperText('Cette actualité sere mise en avant sur le site internet')
                            ->inline(false),

                        Toggle::make('has_notification')
                            ->label('Envoyer une notification')
                            ->helperText("Les personnes et organisations ayant contribué recevront un email avec le contenu de l'actualité.")
                            ->inline(false),
                    ]),
            ])
            ->action(function (array $data) {
                $newsService = new NewsService();
                $newsService->store([
                    ...$data,
                    'project_id' => $data['project_id'] ?? $this->project->id,
                ]);

                $this->dispatch('newsAdded');

                Notification::make()
                    ->title('Actualité ajoutée')
                    ->body('Vous pouvez la modifier dans la table sur cette même page')
                    ->success()
                    ->send();
            })
            ->model(News::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.news.create-form');
    }
}
