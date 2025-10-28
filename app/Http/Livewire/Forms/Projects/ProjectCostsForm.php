<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Enums\Models\Projects\CarbonCreditCharacteristicsEnum;
use App\Enums\Models\Projects\CreditTemporalityEnum;
use App\Enums\Roles;
use App\Helpers\ActivityHelper;
use App\Helpers\TVAHelper;
use App\Models\Project;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ProjectCostsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Project $project;

    public function mount()
    {
        $this->project->tenant_commission_type = $this->project->tenant_commission_type ?: CommissionTypeEnum::Percentage->databaseKey();

        $baseArray = $this->project->toArray();

        $baseArray['tenant_commission_type'] = match ($this->project->hasParent()) {
            true => $this->project->parentProject->tenant_commission_type?->value,
            false => $this->project->tenant_commission_type?->value,
        };

        $baseArray['tenant_commission_numerical'] = match ($this->project->hasParent()) {
            true => $this->project->parentProject->tenant_commission_numerical,
            false => $this->project->tenant_commission_numerical,
        };

        $baseArray['tenant_commission_percentage'] = match ($this->project->hasParent()) {
            true => $this->project->parentProject->tenant_commission_percentage,
            false => $this->project->tenant_commission_percentage,
        };

        $this->form->fill([
            'project' => $baseArray,
            'goal_tco2' => match ($this->project->hasChildrenProjects()) {
                true => match ($this->project->is_goal_tco2_edited_manually) {
                    true => $this->project->tco2,
                    false => $this->project->childrenProjects->sum('tco2')
                },
                false => $this->project->tco2
            },
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->disabled(! request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
                ->schema([
                    Fieldset::make('goal')
                        ->label("Objectifs de réductions d'émissions")
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema([
                            Toggle::make('project.is_goal_tco2_edited_manually')
                                ->label('Objectifs de CO2 édités manuellement')
                                ->visible($this->project->hasChildrenProjects())
                                ->columnSpanFull()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $goalTco2 = match ($this->project->hasChildrenProjects()) {
                                        true => match ($state) {
                                            true => $this->project->tco2,
                                            false => $this->project->childrenProjects->sum('tco2')
                                        },
                                        false => $this->project->tco2
                                    };

                                    $set('goal_tco2', $goalTco2);
                                })
                                ->helperText("Si l'objectif est géré manuellement, vous devez vous-même mettre à jour si vous ajoutez des sous-projets. Dans le cas contraire, cela est calculé automatiquement.")
                                ->reactive(),

                            TextInput::make('goal_tco2')
                                ->label('Objectif en tonne de CO2')
                                ->reactive()
                                ->hint(function (Get $get) {
                                    if ($this->project->hasChildrenProjects() and ! $get('project.is_goal_tco2_edited_manually')) {
                                        return new HtmlString("<span class='text-yellow-500'>Calculé automatiquement</span>");
                                    }

                                    return null;
                                })
                                ->readOnly(fn (Get $get): bool => $this->project->hasChildrenProjects() and ! $get('project.is_goal_tco2_edited_manually'))
                                ->required(fn (Get $get): bool => ($this->project->hasChildrenProjects() and $get('project.is_goal_tco2_edited_manually') or ! $this->project->hasChildrenProjects()))
                                ->helperText($this->project->hasChildrenProjects() ? 'Somme actuelle des objectifs tCo2 des sous-projets : '.$this->project->childrenProjects->sum('tco2').' tcO2' : null)
                                ->numeric(),

                            TextInput::make('project.cost_duration_years')
                                ->label('Durée de réduction des émissions')
                                ->required()
                                ->suffix('années')
                                ->minValue(1)
                                ->numeric(),
                        ]),

                    Fieldset::make('goal')
                        ->label('Coût global & commissions')
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema(components: [
                            Toggle::make('project.subject_to_vat')
                                ->label('Porteur assujetti à la TVA ?')
                                ->inline(false)
                                ->columnSpanFull()
                                ->onColor('success')
                                ->offColor('danger')
                                ->reactive()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $set('project.amount_wanted_ttc', function () use ($get, $state) {
                                        if ($state) {
                                            return TVAHelper::getTTC($get('project.amount_wanted') ?? 0);
                                        }

                                        return $get('project.amount_wanted');
                                    });

                                    $set('project.cost_global_ttc', function ($state) use ($get) {
                                        $amountWantedTtc = $get('project.amount_wanted_ttc');

                                        $costGlobalTTc = $amountWantedTtc + TVAHelper::getTTC(self::getTenantCommission($get));

                                        return round($costGlobalTTc, 2);
                                    });
                                }),

                            Select::make('project.tenant_commission_type')
                                ->label('Type de commission antenne')
                                ->searchable()
                                ->required(! $this->project->hasParent())
                                ->disabled($this->project->hasParent())
                                ->reactive()
                                ->options(CommissionTypeEnum::toArray()),

                            TextInput::make('project.tenant_commission_percentage')
                                ->label('Pourcentage de la commission antenne')
                                ->suffix('%')
                                ->numeric()
                                ->disabled($this->project->hasParent())
                                ->minValue(0)
                                ->maxValue(100)
                                ->required(fn (Get $get) => CommissionTypeEnum::tryFrom($get('project.tenant_commission_type')) == CommissionTypeEnum::Percentage and ! $this->project->hasParent())
                                ->visible(fn (Get $get) => CommissionTypeEnum::tryFrom($get('project.tenant_commission_type')) == CommissionTypeEnum::Percentage),

                            TextInput::make('project.tenant_commission_numerical')
                                ->label('Montant de la commission (€ HT)')
                                ->suffix(' € HT')
                                ->numeric()
                                ->minValue(0)
                                ->required(fn (Get $get) => CommissionTypeEnum::tryFrom($get('project.tenant_commission_type')) == CommissionTypeEnum::Numerical)
                                ->visible(fn (Get $get) => CommissionTypeEnum::tryFrom($get('project.tenant_commission_type')) == CommissionTypeEnum::Numerical),

                            TextInput::make('project.amount_wanted_ttc')
                                ->label('Somme pour porteur (TTC)')
                                ->required()
                                ->numeric()
                                ->helperText('Le montant TTC est automatiquement calculé en fonction du montant HT.')
                                ->readOnly()
                                ->hint('Calculé automatiquement')
                                ->hintColor('warning')
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Calculé depuis la somme pour porteur HT')
                                ->default(0)
                                ->suffix('€'),

                            TextInput::make('project.amount_wanted')
                                ->label('Somme pour porteur (HT)')
                                ->numeric()
                                ->reactive()
                                ->suffixAction(
                                    FormAction::make('slugify')
                                        ->icon('heroicon-s-arrow-path')
                                        ->action(function (Set $set, Get $get, $state) {
                                            $amountWantedTtc = 0;

                                            if ($get('project.subject_to_vat')) {
                                                $amountWantedTtc = TVAHelper::getTTC($state ?? 0);
                                            } else {
                                                $amountWantedTtc = $state;
                                            }

                                            $set('project.amount_wanted_ttc', $amountWantedTtc);

                                            $set('project.cost_global_ttc', function ($state) use ($get, $amountWantedTtc) {
                                                $amountTtc = $amountWantedTtc + TVAHelper::getTTC(self::getTenantCommission($get));

                                                return round($amountTtc, 2);
                                            });
                                        })
                                )
                                ->suffix('€'),

                            TextInput::make('project.cost_global_ttc')
                                ->label('Montant global (TTC)')
                                ->suffix('€')
                                ->numeric()
                                ->required()
                                ->lazy()
                                ->reactive()
                                ->readOnly()
                                ->hintColor('warning')
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    // Nouvelle logique de calcul selon que le porteur est assujetti à la TVA ou non
                                    if ($get('project.subject_to_vat')) {
                                        // Si assujetti à la TVA : calcul classique
                                        $set('cost_global_ht', TVAHelper::getHT($state));
                                    } else {
                                        // Si non assujetti à la TVA : Montant global HT = Somme porteur HT + Commission HT
                                        $amountWantedHT = $get('project.amount_wanted') ?? 0;
                                        $commissionHT = self::getTenantCommission($get);
                                        $set('cost_global_ht', $amountWantedHT + $commissionHT);
                                    }
                                })
                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Calculé depuis la somme pour porteur TTC et la commission.')
                                ->hint('Calculé automatiquement'),

                            Placeholder::make('cost_global_ht')
                                ->label('Montant global (HT)')
                                ->content(function (Get $get) {
                                    // Nouvelle logique d'affichage selon que le porteur est assujetti à la TVA ou non
                                    if ($get('project.subject_to_vat')) {
                                        // Si assujetti à la TVA : calcul classique
                                        return format(TVAHelper::getHT($get('project.cost_global_ttc'))).' €';
                                    } else {
                                        // Si non assujetti à la TVA : Montant global HT = Somme porteur HT + Commission HT
                                        $amountWantedHT = $get('project.amount_wanted') ?? 0;
                                        $commissionHT = self::getTenantCommission($get);
                                        return format($amountWantedHT + $commissionHT).' €';
                                    }
                                }),

                        ]),

                    Fieldset::make('financing_logic')
                        ->label('Informations sur le financement recherché')
                        ->schema([
                            Placeholder::make('commission')
                                ->label('Commission antenne')
                                ->label(new HtmlString("<span class='font-bold'>Commission antenne</span>"))
                                ->reactive()
                                ->columnSpanFull()
                                ->content(function (Get $get) {
                                    $commissionHT = self::getTenantCommission($get);

                                    return format($commissionHT).' € HT / '.format(TVAHelper::getTTC($commissionHT)).' € TTC';
                                }),

                            Placeholder::make('versements')
                                ->label(new HtmlString("<span class='font-bold'>Versements</span>"))
                                ->reactive()
                                ->columnSpanFull()
                                ->content(function (Get $get) {
                                    if (! $get('goal_tco2') or $get('goal_tco2') == 0 or ! $get('project.amount_wanted') or $get('project.amount_wanted') == 0) {
                                        return new HtmlString("<span class='text-yellow-500'>Vous devez définir un objectif de Co2 et un montant au porteur.</span>");
                                    }

                                    $projectPartners = $this->project->projectPartners()->get();

                                    $output = '';
                                    $sumPartners = 0;

                                    // Calcul du montant global HT selon l'assujettissement à la TVA
                                    if ($get('project.subject_to_vat')) {
                                        $costGlobalHT = TVAHelper::getHT($get('project.cost_global_ttc'));
                                    } else {
                                        // Si non assujetti : Montant global HT = Somme porteur HT + Commission HT
                                        $amountWantedHT = $get('project.amount_wanted') ?? 0;
                                        $commissionHT = self::getTenantCommission($get);
                                        $costGlobalHT = $amountWantedHT + $commissionHT;
                                    }

                                    foreach ($projectPartners as $projectPartner) {
                                        $partnerAmount = match ($projectPartner->commission_type) {
                                            CommissionTypeEnum::Numerical => TVAHelper::getTTC($projectPartner->commission_numerical),
                                            CommissionTypeEnum::Percentage => TVAHelper::getTTC(($projectPartner->commission_percentage / 100) * $costGlobalHT),
                                        };

                                        $output .= "Commission <span class='font-semibold'>".$projectPartner->partner->name.'</span> = '.format($partnerAmount).' € TTC <br>';
                                        $sumPartners += $partnerAmount;
                                    }

                                    $output .= 'Total commissions partenaires : '.format(TVAHelper::getHT($sumPartners)).' € HT / '.format($sumPartners).' € TTC <br><br>';

                                    $output .= "<span class='font-bold'>Résumé</span> <br>";

                                    $output .= 'Pour porteur : '.format($get('project.amount_wanted')).' € HT / '.format($get('project.amount_wanted_ttc')).' € TTC <br>';

                                    $revenueTenant = TVAHelper::getTTC(self::getTenantCommission($get)) - $sumPartners;

                                    $output .= 'Chiffre d\'affaire Coop : '.format($revenueTenant).' € TTC';

                                    return new HtmlString($output);
                                }),
                        ]),

                    Fieldset::make()
                        ->label('Information sur le crédit')
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema([

                            Select::make('project.credit_temporality')
                                ->label('Temporalité du crédit')
                                ->searchable()
                                ->reactive()
                                ->helperText(fn ($state) => $state ? CreditTemporalityEnum::from($state)->description() : null)
                                ->options(CreditTemporalityEnum::toArray()),

                            Select::make('project.credit_characteristics')
                                ->label('Caractéristiques du crédit')
                                ->helperText('Si non-renseignée, la valeur par défaut sera '.CarbonCreditCharacteristicsEnum::TotalGains->displayName())
                                ->searchable()
                                ->options(CarbonCreditCharacteristicsEnum::toArray()),

                            KeyValue::make('project.contracts_with_obligation_to_achieve_results')
                                ->keyLabel('Année')
                                ->valueLabel('Pourcentage (%)')
                                ->valuePlaceholder('Entre 0 et 100 %')
                                ->label("% contrats avec obligation de résultat sur l'année"),
                        ]),

                    Fieldset::make()
                        ->label("Information sur l'audit")
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema([

                            Toggle::make('project.is_audit_done')
                                ->label("L'audit a-t-il été réalisé ?")
                                ->helperText("Cette information permet d'ajuster le calcul du risque."),
                            
                            DatePicker::make('project.planned_audit_year')
                                    ->label("Date prévisionnel d'audit")
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->closeOnDateSelection()
                                    ->nullable(),

                        ]),

                    Fieldset::make()
                        ->label('Paiements au porteur')
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema([

                            TextInput::make('project.holder_amount_give')
                                ->numeric()
                                ->suffix('€ TTC')
                                ->disabled()
                                ->label('Montant déjà donné au porteur (€ TTC)')
                                ->helperText('Somme automatique des montants déjà distribués'),
                        ]),

                ]),

        ];
    }

    public function submit()
    {
        $state = $this->form->getState();

        $this->project->update([
            ...$state['project'],
            'tco2' => (float) $state['goal_tco2'],
        ]);

        Notification::make()
            ->title('Coûts mis à jour.')
            ->body('Les informations de coût lié au projet ont été mis à jour.')
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->project,
            title: 'Mise à jour des coûts de financement',
            url: route('projects.show.costs', ['project' => $this->project])
        );
    }

    public function getPercentageCommission(): float
    {
        if ($this->project->projectPartners()->count() == 0) {
            return 0;
        }

        return $this->project->projectPartners()->where('commission_type', CommissionTypeEnum::Percentage)->sum('commission_percentage') / 100;
    }

    public function getNumericalCommission(): float
    {
        return $this->project->projectPartners()->where('commission_type', CommissionTypeEnum::Numerical)->sum('commission_numerical');
    }

    public static function getTenantCommission($get): float|int
    {
        return match (CommissionTypeEnum::tryFrom($get('project.tenant_commission_type'))) {
            CommissionTypeEnum::Numerical => $get('project.tenant_commission_numerical') ?? 0,
            CommissionTypeEnum::Percentage => ($get('project.amount_wanted') / (1 - ($get('project.tenant_commission_percentage') / 100))) - $get('project.amount_wanted'),
            default => 0,
        };
    }

    public function render()
    {
        return view('livewire.forms.projects.project-costs-form');
    }
}
