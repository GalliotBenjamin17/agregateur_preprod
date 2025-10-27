<?php

namespace App\Models;

use App\Enums\Models\PartnerProjectPayments\PaymentStateEnum;
use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Enums\Models\Projects\CarbonCreditCharacteristicsEnum;
use App\Enums\Models\Projects\CertificationStateEnum;
use App\Enums\Models\Projects\CreditTemporalityEnum;
use App\Enums\Models\Projects\ProjectStateEnum;
use App\Enums\Roles;
use App\Helpers\TVAHelper;
use App\States\Certification\Approved;
use App\States\Certification\Certified;
use App\States\Certification\Verified;
use App\Traits\HasAddress;
use App\Traits\HasCreatedBy;
use App\Traits\HasFiles;
use App\Traits\HasRedirection;
use App\Traits\HasSlug;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

class Project extends Model
{
    use HasAddress, HasCreatedBy, HasFiles, HasRedirection, HasSlug, HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'start_at' => 'date',
        'state' => ProjectStateEnum::class,
        'certification_state' => CertificationStateEnum::class,
        'method_replies' => 'array',
        'can_be_displayed_on_website' => 'boolean',
        'can_be_financed_online' => 'boolean',
        'can_be_displayed_percentage_of_funding' => 'boolean',
        'can_be_displayed_on_terminal' => 'boolean',
        'credit_temporality' => CreditTemporalityEnum::class,
        'credit_characteristics' => CarbonCreditCharacteristicsEnum::class,
        'subject_to_vat' => 'boolean',
        'holder_amount_documents' => 'array',
        'tenant_commission_type' => CommissionTypeEnum::class,
        'is_goal_tco2_edited_manually' => 'boolean',
        'is_audit_done' => 'boolean',
        'contracts_with_obligation_to_achieve_results' => 'array',
        'is_funded' => 'boolean',
        'contributors_files' => 'json',
        'is_semi_financed_notification_sent' => 'boolean',
        'is_fully_financed_notification_sent' => 'boolean',
        'tco2' => 'float',
        'expenses' => 'array',
        'revenues' => 'array',
        'is_synchronized_with_parent' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->state = 'submitted';
            $model->certification_state = 'notified';
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function sponsor(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'sponsor_type', 'sponsor_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'sponsor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function certification(): BelongsTo
    {
        return $this->belongsTo(Certification::class, 'certification_id', 'id');
    }

    public function segmentation(): BelongsTo
    {
        return $this->belongsTo(Segmentation::class, 'segmentation_id', 'id');
    }

    public function sustainableDevelopmentGoals(): BelongsToMany
    {
        return $this->belongsToMany(SustainableDevelopmentGoals::class, 'project_sustainable_development_goals');
    }

    public function childrenProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'parent_project_id', 'id');
    }

    public function referent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referent_id', 'id');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id', 'id');
    }

    public function auditors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_auditors');
    }

    public function parentProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'parent_project_id', 'id');
    }

    public function donationSplits(): HasMany
    {
        return $this->hasMany(DonationSplit::class, 'project_id', 'id');
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'project_id');
    }

    public function methodForm(): BelongsTo
    {
        return $this->belongsTo(MethodForm::class, 'method_form_id', 'id');
    }

    public function carbonPrices(): HasMany
    {
        return $this->hasMany(ProjectCarbonPrice::class, 'project_id', 'id')
            ->orderBy('created_at', 'desc');
    }

    public function activeCarbonPrice(): HasOne
    {
        return $this->hasOne(ProjectCarbonPrice::class, 'project_id', 'id')
            ->orderBy('created_at', 'desc')
            ->whereNull('end_at');
    }

    public function projectPartners(): HasMany
    {
        return $this->hasMany(PartnerProject::class, 'project_id', 'id');
    }

    public function projectHolderPayments(): HasMany
    {
        return $this->hasMany(ProjectHolderPayment::class, 'project_id', 'id');
    }

    public function annualReport(): BelongsTo
    {
        return $this->belongsTo(File::class, 'annual_report_file_id', 'id');
    }

    public function hasParent(): bool
    {
        return $this->parentProject()->exists();
    }

    public function hasChildrenProjects(): bool
    {
        return $this->childrenProjects()->count() > 0;
    }

    public function hasTenant(): bool
    {
        return $this->tenant()->exists();
    }

    public function hasAuditor(): bool
    {
        return $this->auditor()->exists();
    }

    public function hasReferent(): bool
    {
        return $this->referent()->exists();
    }

    public function hasMethodForm(): bool
    {
        return $this->methodForm()->exists();
    }

    public function hasCarbonPrice(): bool
    {
        return $this->activeCarbonPrice()->exists();
    }

    public function scopeActive($query)
    {
        return $query->whereIn('state', [
            'approved',
            'pending',
            'done',
        ]);
    }

    public function getDonationAmountRemaining(): float
    {
        return $this->cost_global_ttc - $this->donationSplits()->sum('amount');
    }

    public function canAffiliateAmount(float $amount): bool
    {
        return $amount <= $this->getDonationAmountRemaining();
    }

    public function hasFormFieldsDisabled(): bool
    {
        return in_array($this->certification_state, [Certified::$name, Verified::$name, Approved::$name])
            or request()->user()->hasAnyRole(Roles::Member, Roles::Partner, Roles::Auditor);
    }

    public function hasFundingAdded(): bool
    {
        return $this->cost_global_ttc &&
                $this->cost_duration_years &&
                $this->amount_wanted_ttc &&
                $this->amount_wanted;
    }

    public function commission(): Attribute
    {
        return Attribute::get(function () {
            if ($this->projectPartners()->count() == 0) {
                return 0;
            }

            return $this->projectPartners()->where('commission_type', CommissionTypeEnum::Numerical)->sum('commission_numerical')
                + $this->amount_wanted * ($this->projectPartners()->where('commission_type', CommissionTypeEnum::Percentage)->sum('commission_percentage') / 100);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accountancy
    |--------------------------------------------------------------------------
    */

    // return en HT
    public function getTenantCommission(): float
    {
        return match ($this->tenant_commission_type) {
            CommissionTypeEnum::Numerical => $this->tenant_commission_numerical ?? 0,
            CommissionTypeEnum::Percentage => ($this->amount_wanted / (1 - ($this->tenant_commission_percentage / 100))) - $this->amount_wanted,
            default => 0,
        };
    }

    public function calculateHolderPaymentsSum(): void
    {
        $this->update([
            'holder_amount_give' => $this->projectHolderPayments()->sum('amount'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Financial export logic
    |--------------------------------------------------------------------------
    */

    /**
     * Année prévisionnelle d'audit
     */
    public function getPlannedAuditYear(): float
    {
        if ($this->planned_audit_year) {
            return $this->planned_audit_year;
        }

        if ($this->start_at) {
            return $this->start_at->addYears(5)->format('Y');
        }

        return $this->created_at->addYears(5)->format('Y');
    }

    /**
     * % de contrats avec obligation de résultat sur une année
     */
    public function getContractsWithObligationToAchieveResults(int $year): float
    {
        if (Arr::has($this->contracts_with_obligation_to_achieve_results, $year)) {
            return (float) $this->contracts_with_obligation_to_achieve_results[$year] / 100;
        }

        return 0;
    }

    /**
     * Toutes les années d'analyses de la première contribution à la dernière
     *
     * @return array<integer>
     *
     * @throws \Exception
     */
    public function getPeriodOfAnalysis(): array
    {
        // Get the earliest and latest dates among donations and payments
        $earliestDate = $this->donationSplits()->min('created_at');
        $latestDonationDate = $this->donationSplits()->max('created_at');
        $latestPaymentDate = $this->projectHolderPayments()->max('created_at');

        // Determine the latest date between donation and payment
        $latestDate = max($latestDonationDate, $latestPaymentDate);

        // Convert dates to DateTime objects
        $startDate = new \DateTime($earliestDate);
        $endDate = new \DateTime($latestDate);

        // Define interval and period
        $interval = new \DateInterval('P1Y');
        $period = new \DatePeriod($startDate, $interval, $endDate);

        // Populate years array
        $years = [];
        foreach ($period as $date) {
            $years[$date->format('Y')] = [];
        }

        // Ensure the end year is included in the array
        $endYear = $endDate->format('Y');

        if (! array_key_exists($endYear, $years)) {
            $years[$endYear] = [];
        }

        return array_keys($years);
    }

    /**
     * Montant théorique dû au porteur sur une année
     */
    public function getProjectHolderTheoreticalAmount(int $year): float
    {
        if ($this->tenant_commission_type == CommissionTypeEnum::Percentage) {
            return $this->getDonations(mode: 'HT', year: $year) - $this->getTenantCommissionFinancial(year: $year);
        }

        $yearsOfAnalysis = $this->getPeriodOfAnalysis();

        if ($yearsOfAnalysis[0] == $year) {
            return $this->getDonations(mode: 'HT', year: $year) - $this->getTenantCommissionFinancial(year: $year);
        }

        return 0;
    }

    /**
     * Montant réel donné au porteur sur une année
     *
     * @param  Project  $project
     */
    public function getProjectHolderRealAmount(int $year): float
    {
        return $this->projectHolderPayments()
            ->whereYear('created_at', $year)
            ->sum('amount_ht');
    }

    /**
     * Montant des contributions reçues sur une année
     */
    public function getDonations(string $mode = 'HT', ?int $year = null): float
    {
        if (! in_array($mode, ['HT', 'TTC'])) {
            return -1;
        }

        $getAllDonations = function () use ($year) {
            if (! $year) {
                return $this->donationSplits()->sum('amount');
            }

            return $this->donationSplits()->whereYear('created_at', $year)->sum('amount');
        };

        if ($mode == 'TTC') {
            return $getAllDonations();
        }

        if ($this->subject_to_vat) {
            return TVAHelper::getHT($getAllDonations());
        }

        // Mode HT et sur le global
        if (is_null($year)) {
            return $getAllDonations() - (0.2 * $this->donationSplits()->sum('amount') * ($this->tenant_commission_percentage / 100));
        }

        if ($this->tenant_commission_type == CommissionTypeEnum::Numerical) {
            return $this->donationSplits()->whereYear('created_at', $year)->sum('amount') - $this->tenant_commission_numerical * 0.2;
        }

        // On applique seulement sur la commission affiliée pour cette année-là
        return $this->donationSplits()->whereYear('created_at', $year)->sum('amount') / (1 + ($this->tenant_commission_percentage / 100) * 0.2);
    }

    /**
     * Montant théorique dû au porteur sur une année
     *
     * @return float en HT
     */
    public function getPartnerTheoreticalAmount(PartnerProject $partnerProject, int $year): float
    {
        if ($partnerProject->commission_type == CommissionTypeEnum::Percentage) {
            return $this->getDonations(mode: 'HT', year: $year) * ($partnerProject->commission_percentage / 100);
        }

        $yearsOfAnalysis = $this->getPeriodOfAnalysis();

        if ($yearsOfAnalysis[0] == $year) {
            return $partnerProject->commission_numerical; // retourné en HT
        }

        return 0;
    }

    /**
     * Montant réel versé au porteur sur une année
     */
    public function getPartnerRealAmount(PartnerProject $partnerProject, int $year): float
    {
        return TVAHelper::getHT($partnerProject->payments()->where('payment_state', PaymentStateEnum::Sent)
            ->whereYear('created_at', $year)
            ->sum('amount') ?? 0);
    }

    /**
     * Commission sur une année
     */
    public function getTenantCommissionFinancial(int $year): float
    {
        $yearsOfAnalysis = $this->getPeriodOfAnalysis();

        return match ($this->tenant_commission_type) {
            CommissionTypeEnum::Percentage => $this->getDonations(mode: 'HT', year: $year) * ($this->tenant_commission_percentage / 100),
            CommissionTypeEnum::Numerical => match ($yearsOfAnalysis[0] == $year) {
                true => $this->tenant_commission_numerical, // Si c'est la première année
                false => 0, // On a déjà renvoyé la valeur sur la première année
            },
            default => 0,
        };
    }

    /**
     * Chiffre d'affaires sur une année
     */
    public function getCA(int $year): float
    {
        $yearlyDonations = $this->getDonations(mode: 'HT', year: $year);

        $partnersProject = $this->projectPartners()->get();

        $output = [
            'partnersProject' => [],
        ];

        $yearsOfAnalysis = $this->getPeriodOfAnalysis();

        foreach ($partnersProject as $partnerProject) {

            $output['partnersProject'][] = [
                'partner_id' => $partnerProject->partner->id,
                'commission_type' => $partnerProject->commission_type->databaseKey(),
                'commission_percentage' => match ($partnerProject->commission_type) {
                    CommissionTypeEnum::Percentage => $yearlyDonations * ($partnerProject->commission_percentage / 100),
                    CommissionTypeEnum::Numerical => match ($yearsOfAnalysis[0] == $year) {
                        true => $partnerProject->commission_numerical, // Si c'est la première année
                        false => 0, // On a déjà renvoyé la valeur sur la première année
                    },
                },
            ];
        }

        // Montant obligatoire que l'on doit aux partenaires
        $mandatoryAmountForPartners = collect($output['partnersProject'])->sum('commission_percentage');

        return $this->getTenantCommissionFinancial(year: $year) - $mandatoryAmountForPartners;
    }

    public function getRisk(int $year): array
    {
        if ($year >= $this->getPlannedAuditYear()) {
            return [
                'value' => 0,
                'type' => 'Audit effectué',
            ];
        }

        $A = $B = $risk1 = 0;
        $C = 0.75;

        $A = $this->getCA(year: $year);

        if ($year < $this->getPlannedAuditYear()) {
            $B = 1;
        }

        if ($this->credit_characteristics == CarbonCreditCharacteristicsEnum::Sequestration) {
            $C = 1;
        } elseif ($this->credit_characteristics == CarbonCreditCharacteristicsEnum::Avoidance) {
            $C = 0.5;
        }

        $risk1 = $A * $B * $C;

        $risk2 = $this->getContractsWithObligationToAchieveResults(year: $year) * $this->getDonations(mode: 'HT', year: $year);

        $paymentsPartnersSumAmount = $this->projectPartners()
            ->with('payments')
            ->get()
            ->pluck('payments')
            ->collapse()
            ->filter(function ($value) use ($year) {
                return $value->created_at->year == $year;
            })
            ->sum('amount');

        $risk3 = $this->projectHolderPayments()->whereYear('created_at', $year)->sum('amount_ht') + TVAHelper::getHT($paymentsPartnersSumAmount);

        $getRiskNumber = function ($risk1, $risk2, $risk3) {
            $maxValue = max($risk1, $risk2, $risk3);

            if ($maxValue == $risk1) {
                return '1'; // Première valeur est la plus grande
            } elseif ($maxValue == $risk2) {
                return '2'; // Deuxième valeur est la plus grande
            } else {
                return '3'; // Troisième valeur est la plus grande
            }
        };

        return [
            'value' => max($risk1, $risk2, $risk3),
            'type' => $getRiskNumber($risk1, $risk2, $risk3),
        ];
    }

    public function globalCalculus(): array
    {
        $yearsOfAnalysis = $this->getPeriodOfAnalysis();
        $yearlyInformations = [];

        $projectPartners = $this->projectPartners()->get();

        $partnersYearlyArray = [];

        foreach ($projectPartners as $projectPartner) {
            $partnersYearlyArray[$projectPartner->partner_id] = [
                'theoretical_amount' => 0,
                'real_amount' => 0,
                'withdrawal' => 0,
                'spill_amount_previous_year' => 0,
                'spill_amount_next_year' => 0,
                'commission_type' => $projectPartner->commission_type->databaseKey(),
            ];
        }

        foreach ($yearsOfAnalysis as &$yearOfAnalysis) {

            $yearlyInformations[$yearOfAnalysis] = [
                'donations' => $this->getDonations(mode: 'HT', year: $yearOfAnalysis),
                'ca' => 0,
                'tenant' => [
                    'theoretical_amount' => 0,
                    'real_amount' => 0,
                    'spill_amount_next_year' => 0,
                    'spill_amount_previous_year' => 0,
                ],
                'project_holder' => [
                    'theoretical_amount' => 0,
                    'real_amount' => 0,
                ],
                'partners' => $partnersYearlyArray,
                'risk' => 0,
            ];

        }

        foreach ($yearlyInformations as $year => &$values) {

            // On initialise tout
            $values['tenant']['theoretical_amount'] = $this->getTenantCommissionFinancial(year: $year);
            $values['tenant']['real_amount'] = $this->getTenantCommissionFinancial(year: $year);

            $values['project_holder']['real_amount'] = $this->getProjectHolderTheoreticalAmount(year: $year);

            foreach ($values['partners'] as $partnerId => &$partner) {
                $partner['theoretical_amount'] = $this->getPartnerTheoreticalAmount(partnerProject: $projectPartners->where('partner_id', $partnerId)->first(), year: $year);
                $partner['real_amount'] = 0;
                $partner['withdrawal'] = $this->getPartnerTheoreticalAmount(partnerProject: $projectPartners->where('partner_id', $partnerId)->first(), year: $year);
                $partner['spill_amount_next_year'] = 0; // Va être choisi au moment de l'écumage
            }

            $values['ca'] = $this->getCA(year: $year);
        }

        // On écume
        foreach ($yearlyInformations as $year => &$values) {
            $amountToReduce = match (($values['ca'] ?? 0) >= 0) {
                true => 0,
                false => abs($values['ca'])
            };

            try {
                $partnersYearlyArray = array_filter($values['partners'], function ($partner) {
                    return $partner['theoretical_amount'] >= 0 and $partner['commission_type'] == CommissionTypeEnum::Numerical->databaseKey();
                });
            } catch (\ErrorException $e) {
                continue;
            }

            $totalTheoreticalAmount = array_sum(array_column($partnersYearlyArray, 'theoretical_amount'));

            foreach ($partnersYearlyArray as $partnerId => &$partner) {

                if ($amountToReduce <= 0) {
                    continue;
                }

                $amount = match ($totalTheoreticalAmount == 0) {
                    true => 0,
                    false => $partner['theoretical_amount'] * ($amountToReduce / $totalTheoreticalAmount)
                };

                $yearlyInformations[$year + 1]['partners'][$partnerId]['spill_amount_previous_year'] = $amount;

                if (Arr::has($yearlyInformations, strval($year + 1))) {
                    $values['partners'][$partnerId]['spill_amount_next_year'] = $amount;
                }

                $values['partners'][$partnerId]['real_amount'] = $values['partners'][$partnerId]['spill_amount_previous_year'] + ($values['partners'][$partnerId]['theoretical_amount'] - $amount);
            }

            $values['ca'] = $values['tenant']['real_amount'] - array_sum(Arr::pluck($values['partners'], 'real_amount'));

            $partnersYearlyNumericalArray = array_filter($values['partners'], function ($partner) {
                return $partner['commission_type'] == CommissionTypeEnum::Numerical->databaseKey() and $partner['spill_amount_next_year'] >= 0;
            });

            if ($values['ca'] >= 0 and array_sum(array_column($partnersYearlyArray, 'spill_amount_previous_year')) > array_sum(array_column($partnersYearlyArray, 'real_amount'))) {

                $availableAmount = $values['ca'];

                $partnersYearlyArray = array_filter($values['partners'], function ($partner) {
                    return $partner['spill_amount_next_year'] >= 0 and $partner['commission_type'] == CommissionTypeEnum::Numerical->databaseKey();
                });

                $totalTheoreticalAmount = array_sum(array_column($partnersYearlyArray, 'real_amount'));

                foreach ($partnersYearlyArray as $partnerId => &$partner) {

                    $amount = match ($totalTheoreticalAmount == 0) {
                        true => $partner['real_amount'] + ($availableAmount / count($partnersYearlyArray)),
                        false => $partner['real_amount'] + ($availableAmount * ($availableAmount / $totalTheoreticalAmount)),
                    };

                    $amountAlreadyAffiliated = 0;
                    $theoreticalAmount = $yearlyInformations[$yearsOfAnalysis[0]]['partners'][$partnerId]['theoretical_amount'];
                    foreach ($yearlyInformations as $subYear => $subValues) {
                        foreach ($subValues['partners'] as $subPartnerId => $subPartner) {
                            if ($subPartnerId == $partnerId and $subYear < $year) {
                                $amountAlreadyAffiliated = $amountAlreadyAffiliated + $subPartner['real_amount'];
                            }
                        }
                    }

                    if ($amount > $theoreticalAmount - $amountAlreadyAffiliated) {
                        $amount = $theoreticalAmount - $amountAlreadyAffiliated;
                    }

                    $values['partners'][$partnerId]['real_amount'] = $values['partners'][$partnerId]['real_amount'] + $amount;

                    $values['partners'][$partnerId]['spill_amount_next_year'] = $values['partners'][$partnerId]['spill_amount_previous_year'] - $values['partners'][$partnerId]['real_amount'];

                    if (Arr::has($yearlyInformations, strval($year + 1))) {
                        $yearlyInformations[$year + 1]['partners'][$partnerId]['spill_amount_previous_year'] = $values['partners'][$partnerId]['spill_amount_next_year'];
                    }
                }
            }

            $values['ca'] = $values['tenant']['real_amount'] - array_sum(Arr::pluck($values['partners'], 'real_amount'));
        }

        // Calcul du risque
        foreach ($yearlyInformations as $year => &$values) {
            if ($year >= $this->getPlannedAuditYear()) {
                $values['risk'] = 0;
            }

            $A = $B = $risk1 = 0;
            $C = 0.75;

            try {
                $A = $values['ca'];
            } catch (\ErrorException $e) {
                continue;
            }

            if ($year < $this->getPlannedAuditYear()) {
                $B = 1;
            }

            if ($this->credit_characteristics == CarbonCreditCharacteristicsEnum::Sequestration) {
                $C = 1;
            } elseif ($this->credit_characteristics == CarbonCreditCharacteristicsEnum::Avoidance) {
                $C = 0.5;
            }

            $risk1 = $A * $B * $C;

            $risk2 = $this->getContractsWithObligationToAchieveResults(year: $year) * $values['donations'];

            $paymentsPartnersSumAmount = $this->projectPartners()
                ->with('payments')
                ->get()
                ->pluck('payments')
                ->collapse()
                ->filter(function ($value) use ($year) {
                    return $value->created_at->year == $year;
                })
                ->sum('amount');

            $risk3 = $this->projectHolderPayments()->whereYear('created_at', $year)->sum('amount_ht') + TVAHelper::getHT($paymentsPartnersSumAmount);

            $values['risk'] = [
                'value' => max($risk1, $risk2, $risk3),
                'type' => [$risk1, $risk2, $risk3],
            ];
        }

        return $yearlyInformations;
    }

    public function getOrganizationsAndUsersEmails(): array
    {

        $donations = $this->donationSplits->load('donation')->pluck('donation');
        $organizations = Organization::with('users')->whereIn('id', $donations->where('related_type', Organization::class)->pluck('related_id'))->get();
        $users = User::whereIn('id', $donations->where('related_type', User::class)->pluck('related_id'))->get();

        return collect([
            ...$organizations->pluck('contact_email'),
            ...$organizations->pluck('users')->collapse()->pluck('email'),
            ...$users->pluck('email'),
        ])->unique()->filter()->toArray();
    }
}
