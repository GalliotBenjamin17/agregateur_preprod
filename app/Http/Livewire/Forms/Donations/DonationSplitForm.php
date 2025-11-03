<?php

namespace App\Http\Livewire\Forms\Donations;

use App\Exceptions\DonationSplitAmountIsNullException;
use App\Exceptions\MoreDonationSplitAmountThanExpectedException;
use App\Helpers\DonationHelper;
use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\Project;
use App\Traits\Filament\HasDataState;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class DonationSplitForm extends Component implements HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }

    public Donation $donation;

    public array $projects = [];

    public array $subProjects = [];

    public float $maximumAvailable = 0;

    public function mount()
    {
        $this->projects = Project::select(['id', 'name', 'parent_project_id', 'amount_wanted_ttc', 'cost_global_ttc'])
            ->whereNull('parent_project_id')
            ->orHas('childrenProjects')
            ->withSum('donationSplits', 'amount')
            ->withCount('childrenProjects')
            ->get()
            ->filter(function ($item) {
                if ($item->children_projects_count > 0) {
                    return true;
                }

                if (is_null($item->cost_global_ttc)) {
                    return false;
                }

                return $item->donation_splits_sum_amount < $item->cost_global_ttc;
            })
            ->pluck('name', 'id')
            ->toArray();

        $this->maximumAvailable = $this->donation->getAvailableAmount();
    }

    protected function getFormSchema(): array
    {
        return [
            Builder::make('splits')
                ->label('Fléchages')
                ->blocks([
                    Builder\Block::make('project')
                        ->label('Fléchage sur un projet')
                        ->schema([
                            Select::make('project_id')
                                ->label('Projet')
                                ->required()
                                ->searchable()
                                ->reactive()
                                ->helperText(function ($state) {
                                    $project = Project::with('activeCarbonPrice')->withSum('donationSplits', 'amount')->find($state);
                                    $price = $project?->activeCarbonPrice->price;
                                    $available = $project?->cost_global_ttc - $project?->donation_splits_sum_amount;

                                    if (! $price) {
                                        return '';
                                    }

                                    if ($available < 0) {
                                        $available = 0;
                                    }

                                    return new HtmlString("<span class='font-semibold'>Prix à la tonne :</span> " . format(TVAHelper::getTTC($price), 2) . " € TTC.<br> <span class='font-semibold'>Reste à financer : </span>" . format($available, 2) . ' € TTC.');
                                })
                                ->options($this->projects),

                            TextInput::make('amount')
                                ->label('Montant TTC')
                                ->numeric()
                                ->helperText(function (\Filament\Forms\Get $get, $state) {
                                    $project = Project::with('activeCarbonPrice')->withSum('donationSplits', 'amount')->find($get('project_id'));
                                    $price = $project?->activeCarbonPrice->price;

                                    $carbonEmission = $state / TVAHelper::getTTC($price ?? 1);

                                    if ($project && $state) {
                                        return new HtmlString('<span class="font-semibold">Disponible après fléchage : </span>' . format($this->maximumAvailable - $state, 2) . ' € TTC <br><span class="font-semibold">tCO2 fléchage :</span> ' . format($carbonEmission, 2) . ' tonnes.');
                                    }

                                    return new HtmlString('<span class="font-semibold">Disponible contribution : </span>' . format($this->maximumAvailable, 2) . ' € TTC');
                                })
                                ->reactive()
                                ->maxValue($this->maximumAvailable)
                                ->required()
                                ->hintAction(
                                    Action::make('set100percentage')
                                        ->icon('heroicon-m-arrow-uturn-down')
                                        ->label('100% de la contribution')
                                        ->action(function (Set $set, $state) {
                                            $set('amount', $this->maximumAvailable);
                                        })
                                ),
                        ])
                        ->columns(2)
                        ->icon('heroicon-s-document-text'),

                        Builder\Block::make('sub_project')
                            ->label('Fléchage sur un sous-projet')
                            ->schema([
                                // 1) Sélection du PROJET PARENT (ne s’appelle plus project_id)
                                Select::make('parent_project_id')
                                    ->label('Projet parent')
                                    ->required()
                                    ->reactive()
                                    ->searchable()
                                    ->helperText(function ($state) {
                                        $project = Project::with('activeCarbonPrice')->withSum('donationSplits', 'amount')->find($state);
                                        $price = $project?->activeCarbonPrice->price;
                                        $available = $project?->cost_global_ttc - $project?->donation_splits_sum_amount;

                                        if (! $price) return '';

                                        if ($available < 0) $available = 0;

                                        return new HtmlString(
                                            "<span class='font-semibold'>Prix à la tonne :</span> " . format($price, 2) . " € HT.<br>" .
                                            "<span class='font-semibold'>Reste à financer : </span>" . format($available, 2) . ' € TTC.'
                                        );
                                    })
                                    // (optionnel) lister uniquement les parents qui ont des sous-projets
                                    ->options(
                                        Project::select('id','name')
                                            ->whereNull('parent_project_id')
                                            ->has('childrenProjects')
                                            ->orderBy('name')
                                            ->pluck('name','id')
                                            ->toArray()
                                    ),

                                // 2) Sélection du SOUS‑PROJET — NOTE : s’appelle maintenant project_id
                                //    => c’est CET ID que l’helper utilisera pour créer le split
                                Select::make('project_id')
                                    ->label('Sous-projet')
                                    ->required()
                                    ->searchable()
                                    ->visible(fn (\Filament\Forms\Get $get) => ! is_null($get('parent_project_id')))
                                    ->reactive()
                                    ->options(function (\Filament\Forms\Get $get) {
                                        return Project::where('parent_project_id', $get('parent_project_id'))
                                            ->withSum('donationSplits', 'amount')
                                            ->get()
                                            ->filter(function ($item) {
                                                if (is_null($item->cost_global_ttc)) return false;
                                                return $item->donation_splits_sum_amount < $item->cost_global_ttc;
                                            })
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->helperText(function ($state) {
                                        // $state = ID du sous-projet
                                        $subProject = Project::with('activeCarbonPrice')
                                            ->withSum('donationSplits', 'amount')
                                            ->find($state);

                                        if (! $subProject) return '';

                                        $wantedTtc     = $subProject->cost_global_ttc;
                                        $alreadyFunded = $subProject->donation_splits_sum_amount ?? 0;

                                        if (! is_null($wantedTtc)) {
                                            $available = max(0, $wantedTtc - $alreadyFunded);
                                            return new HtmlString("<span class='font-semibold'>Reste à financer :</span> " . format($available, 2) . " € TTC.");
                                        }

                                        return '';
                                    }),

                                // 3) Montant
                                TextInput::make('amount')
                                    ->label('Montant TTC')
                                    ->numeric()
                                    ->maxValue($this->donation->amount)
                                    ->required(),
                            ])
                            ->columns()
                            ->icon('heroicon-s-document-duplicate'),


                ])
                ->addActionLabel('Ajouter un fléchage')
                ->minItems(1)
                ->maxItems(10),

        ];
    }

    public function submit()
    {
        try {
            DonationHelper::buildSplit(donation: $this->donation, splits: $this->form->getState()['splits']);
        } catch (DonationSplitAmountIsNullException $exception) {
            Session::flash('alert', 'Vous essayer de flécher sur un projet déjà complété à 100%.');

            return;
        } catch (MoreDonationSplitAmountThanExpectedException $exception) {
            Session::flash('alert', "Vous essayer de flécher plus que la contribution ne le permet. Réduisez le montant de l'un des fléchages.");

            return;
        }

        Session::flash('success', 'Les informations de fléchage ont été ajouté et sont disponibles.');

        return redirect()->route('donations.show.split', ['donation' => $this->donation]);
    }

    public function render()
    {
        return view('livewire.forms.donations.donation-split-form');
    }
}