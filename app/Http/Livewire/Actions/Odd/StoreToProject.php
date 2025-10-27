<?php

namespace App\Http\Livewire\Actions\Odd;

use App\Enums\Roles;
use App\Models\Project;
use App\Models\SustainableDevelopmentGoals;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Livewire\Component;

class StoreToProject extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Project $project;

    public string $redirectUrl;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Affilier des ODD')
            ->size('sm')
            ->color('gray')
            ->size(ActionSize::ExtraSmall)
            ->icon('heroicon-m-plus-circle')
            ->visible(request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
            ->mountUsing(function (ComponentContainer $form) {
                $form->fill([
                    'odd_ids' => $this->project->sustainableDevelopmentGoals()->pluck('id')->toArray(),
                ]);
            })
            ->form([
                Grid::make()
                    ->schema([
                        Select::make('odd_ids')
                            ->label('ODD')
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->columnSpanFull()
                            ->required()
                            ->allowHtml()
                            ->getOptionLabelFromRecordUsing(function (SustainableDevelopmentGoals $sustainableDevelopmentGoals) {
                                return '<div class="flex items-center space-x-5">'.
                                    '<img class="h-6" src="'.asset($sustainableDevelopmentGoals->image).'" />'.
                                    '<span class="">'.$sustainableDevelopmentGoals->name.'</span> </div>';
                            })
                            ->relationship('sustainableDevelopmentGoals', 'name'),

                    ]),
            ])
            ->action(function (Component $livewire, array $data) {

                $this->project->sustainableDevelopmentGoals()->sync($livewire->mountedActionsData[0]['odd_ids']);

                return redirect()->to($this->redirectUrl);
            })
            ->model(Project::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.odd.store-to-project');
    }
}
