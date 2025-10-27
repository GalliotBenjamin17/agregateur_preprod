<?php

namespace App\Http\Livewire\Tables\News;

use App\Enums\Models\News\NewsStateEnum;
use App\Enums\Roles;
use App\Models\News;
use App\Models\Project;
use App\Services\Models\NewsService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class IndexTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Project $project = null;

    protected $listeners = [
        'newsAdded' => 'render',
    ];

    protected function getTableQuery(): Builder
    {
        return News::with([
            'project',
            'author',
        ])
            ->when($this->project, function ($query) {
                return $query->where('project_id', $this->project->id);
            });
    }

    protected function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->modalHeading(function (News $news) {
                    return $news->name;
                })
                ->slideOver()
                ->label('Détails')
                ->color('info')
                ->icon(null)
                ->form(function (News $news) {
                    return [
                        Grid::make()
                            ->schema([
                                Placeholder::make('content')
                                    ->content($news->state->displayName())
                                    ->label('Statut'),

                                Placeholder::make('scheduled_at')
                                    ->content($news->scheduled_at?->format('H:i d/m/Y'))
                                    ->visible(! is_null($news->scheduled_at))
                                    ->label('Planifié à'),

                                Placeholder::make('author')
                                    ->content($news->author?->name ?? '-')
                                    ->label('Auteur'),

                                RichEditor::make('content')
                                    ->columnSpanFull()
                                    ->disabled()
                                    ->default(new HtmlString($news->content))
                                    ->label('Contenu'),
                            ]),
                    ];
                }),
            ActionGroup::make([

                Action::make('Modifier')
                    ->mountUsing(function (ComponentContainer $form, News $news) {
                        $form->fill([
                            'news' => $news->toArray(),
                        ]);
                    })
                    ->action(function (News $news, array $data): void {
                        $newsService = new NewsService(news: $news);

                        $newsService->update([
                            ...$data['news'],
                            'scheduled_at' => NewsStateEnum::from($data['news']['state']) == NewsStateEnum::Scheduled ? $data['news']['scheduled_at'] : null,
                        ]);

                        Notification::make()
                            ->title('Actualité mise à jour')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Grid::make()
                            ->schema([
                                Select::make('news.project_id')
                                    ->label('Projet')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull()
                                    ->required(is_null($this->project))
                                    ->visible(is_null($this->project))
                                    ->relationship('project', 'name', fn (Builder $query) => $query->when(userHasTenant(), function ($query) {
                                        return $query->where('tenant_id', userTenantId());
                                    })),

                                TextInput::make('news.name')
                                    ->label('Titre')
                                    ->columnSpanFull()
                                    ->required(),

                                Select::make('news.state')
                                    ->searchable()
                                    ->label('Statut')
                                    ->reactive()
                                    ->required()
                                    ->options(NewsStateEnum::toArray()),

                                DateTimePicker::make('news.scheduled_at')
                                    ->label('Planifié à')
                                    ->native()
                                    ->seconds(false)
                                    ->required(fn (Get $get) => $get('news.state') == NewsStateEnum::Scheduled->databaseKey())
                                    ->visible(fn (Get $get) => $get('news.state') == NewsStateEnum::Scheduled->databaseKey()),

                                RichEditor::make('news.content')
                                    ->columnSpanFull()
                                    ->required()
                                    ->label('Contenu'),

                                Select::make('news.author_id')
                                    ->label('Auteur')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                                    ->relationship('author', 'first_name', fn (Builder $query) => $query->when(userHasTenant(), function ($query) {
                                        return $query->where('tenant_id', userTenantId());
                                    })),

                                Toggle::make('news.is_featured')
                                    ->label('Mise en avant')
                                    ->helperText('Cette actualité sere mise en avant sur le site internet')
                                    ->inline(false),
                            ]),
                    ])
                    ->slideOver()
                    ->modalSubmitActionLabel('Mettre à jour')
                    ->modalHeading("Mise à jour de l'actualité"),

                DeleteAction::make()
                    ->modalHeading(function (News $news) {
                        return 'Êtes-vous sûr de vouloir supprimer cette actualité ?';
                    })
                    ->requiresConfirmation(),
            ])->visible(request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin])),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('project_filter')
                ->searchable()
                ->visible(is_null($this->project))
                ->label('Projet')
                ->relationship('project', 'name'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('project.name')
                ->label('Projet')
                ->limit(25)
                ->visible(is_null($this->project)),

            TextColumn::make('name')
                ->label('Nom')
                ->limit(50)
                ->description(function (News $record): string {
                    return Str::limit(strip_tags($record->content), 50);
                })
                ->searchable(['name', 'content']),

            TextColumn::make('state')
                ->formatStateUsing(function (News $news) {
                    return $news->state->displayName();
                })
                ->weight('semibold')
                ->icon(function (News $news) {
                    return match ($news->state) {
                        NewsStateEnum::Draft => 'heroicon-s-pencil-square',
                        NewsStateEnum::Scheduled => 'heroicon-s-clock',
                        NewsStateEnum::Published => 'heroicon-s-check-circle',
                    };
                })
                ->description(function (News $news) {
                    return match ($news->state) {
                        NewsStateEnum::Draft, NewsStateEnum::Published => null,
                        NewsStateEnum::Scheduled => $news->scheduled_at?->format('H:i d/m/Y'),
                    };
                })
                ->color(function (News $news) {
                    return match ($news->state) {
                        NewsStateEnum::Draft => 'warning',
                        NewsStateEnum::Scheduled => 'info',
                        NewsStateEnum::Published => 'success',
                    };
                }),

            TextColumn::make('author.name')
                ->default('-')
                ->label('Auteur'),

            TextColumn::make('createdBy.name')
                ->label('Création')
                ->default('Inconnu')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('\A H:i \l\e d/m/Y');
                })
                ->sortable(['created_at']),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return (bool) ! $this->project;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'updated_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function render()
    {
        return view('livewire.tables.news.index-table');
    }
}
