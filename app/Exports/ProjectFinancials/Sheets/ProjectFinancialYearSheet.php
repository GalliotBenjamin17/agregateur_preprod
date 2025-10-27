<?php

namespace App\Exports\ProjectFinancials\Sheets;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Enums\Models\Projects\CarbonCreditCharacteristicsEnum;
use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectFinancialYearSheet implements FromCollection, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles, WithTitle
{
    public function __construct(
        public int $year,
        public Tenant $tenant,
    ) {
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', 'Sur la base des contributions perçues en :');
                $event->sheet->setCellValue('B1', $this->year);

                $event->sheet->setCellValue('A2', "Date de l'extraction");
                $event->sheet->setCellValue('B2', now()->format('d/m/Y H:i'));

                $event->sheet->setCellValue('A3', 'Exporté par');
                $event->sheet->setCellValue('B3', request()->user()?->name);

                $event->sheet->setCellValue('A4', 'Tableau utilisé pour envoi comptable');
                $event->sheet->setCellValue('B4', 'OUI / NON');

                $event->sheet->setCellValue('D1', "Total reçu sur l'année (€ TTC)");
                $event->sheet->setCellValue('E1', $this->getYearlyDonation(mode: 'TTC'));

                $event->sheet->setCellValue('D2', "Total reçu sur l'année (€ HT)");
                $event->sheet->setCellValue('E2', $this->getYearlyDonation(mode: 'HT'));

                $event->sheet->setCellValue('D3', 'TVA collectée');
                $event->sheet->setCellValue('E3', $this->getYearlyDonation(mode: 'TTC') - $this->getYearlyDonation(mode: 'HT'));

                $event->sheet->setCellValue('B6', 'PROJET');
                $event->sheet->mergeCells('B6:D6');

                $event->sheet->setCellValue('E6', 'CA');

                $event->sheet->setCellValue('F6', 'RISQUE');
                $event->sheet->mergeCells('F6:J6');

                $event->sheet->getRowDimension(6)->setRowHeight(20);

                $event->sheet->getColumnDimension('A')->setWidth(40);
                $event->sheet->getColumnDimension('B')->setWidth(25);
                $event->sheet->getColumnDimension('C')->setWidth(25);
                $event->sheet->getColumnDimension('D')->setWidth(25);
                $event->sheet->getColumnDimension('E')->setWidth(25);
                $event->sheet->getColumnDimension('F')->setWidth(25);
                $event->sheet->getColumnDimension('G')->setWidth(25);
                $event->sheet->getColumnDimension('H')->setWidth(25);
                $event->sheet->getColumnDimension('I')->setWidth(25);
                $event->sheet->getColumnDimension('J')->setWidth(25);
                $event->sheet->getColumnDimension('K')->setWidth(25);

                $partnersCount = $this->getPartners(count: true);

                if ($partnersCount <= 2) {
                    $event->sheet->getRowDimension(7)->setRowHeight(45);
                }

                $partnersRangeArray = self::getColumnRange('L', $partnersCount * 2);

                foreach ($partnersRangeArray as $column) {
                    $event->sheet->getColumnDimension($column)->setWidth(25);
                }

                $event->sheet->setCellValue('K6', 'LES COMPTES POUR TIERS');
                $event->sheet->mergeCells('K6:'.Arr::last($partnersRangeArray).'6');

                $rootColumn = 'M';

                $event->sheet->setCellValue('M7', 'Répartition de la contribution reçue (HT) en '.$this->year);
                $event->sheet->mergeCells(self::getCellFormatPartnerScope(1, 7, $rootColumn));

                $rootColumn = self::getColumnLetter($rootColumn, $partnersCount);
                $event->sheet->setCellValue($rootColumn.'7', 'Répartition de la contribution versée (HT) en '.$this->year);
                $event->sheet->mergeCells(self::getCellFormatPartnerScope(2, 7, $rootColumn));
            },
        ];
    }

    public function title(): string
    {
        return (string) $this->year;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Project::whereHas('donationSplits', function ($query) {
            return $query->whereYear('created_at', '<=', $this->year);
        })->get();
    }

    /**
     * @param  Project  $row
     */
    public function map($row): array
    {
        \Log::channel('daily')->info('Export '.$this->year.' : '.$row->name);
        $informations = $row->globalCalculus();
        $yearlyInformations = $informations[$this->year] ?? [];

        $returnArray = [
            $row->name,
            $row->sponsor?->name ?? '',
            $row->subject_to_vat ? 'OUI' : 'NON',
            $this->getPlannedAuditYear(project: $row),
            Arr::get($yearlyInformations, 'ca'),
            $this->getContractsWithObligationToAchieveResults(project: $row, year: $this->year),
            $row->credit_characteristics?->displayName() ?? CarbonCreditCharacteristicsEnum::TotalGains->displayName(),
            Arr::get($yearlyInformations, 'risk.value'),
            0,
            0,
            Arr::get($yearlyInformations, 'project_holder.real_amount'),
            $row->getProjectHolderRealAmount(year: $this->year),
        ];

        $partners = [];

        $rowPartners = $row->projectPartners()->get();

        $getRowPartner = function (Partner $partner) use ($rowPartners): ?PartnerProject {
            return $rowPartners->where('partner_id', $partner->id)->first();
        };

        // A verser
        foreach ($this->getPartners() as $partner) {

            $partnerProject = $getRowPartner($partner);

            if ($partnerProject) {
                $partners[] = Arr::get($yearlyInformations, 'partners.'.$partnerProject->partner_id.'.real_amount');
            } else {
                $partners[] = 0;
            }

        }

        // Reçus en réel par le partenaire
        foreach ($this->getPartners() as $partner) {

            $partnerProject = $getRowPartner($partner);

            if ($partnerProject) {
                $partners[] = $row->getPartnerRealAmount(partnerProject: $partnerProject, year: $this->year);
            } else {
                $partners[] = 0;
            }

        }

        $lastColumns = [
            Arr::get($yearlyInformations, 'donations'),
            Arr::get($yearlyInformations, 'tenant.real_amount'),
            $row->tenant_commission_type?->displayName() ?? 'Non défini',
            match ($row->tenant_commission_type) {
                CommissionTypeEnum::Percentage => $row->tenant_commission_percentage.' %',
                CommissionTypeEnum::Numerical => $row->tenant_commission_numerical.' €',
                default => 'Non défini'
            },
        ];

        return [
            ...$returnArray,
            ...$partners,
            ...$lastColumns,
        ];
    }

    public function headings(): array
    {
        $returnArray = [
            'Projets',
            'Porteur de projet',
            'Assujetti TVA',
            "Année prévisionnelle d'audit",
            "Chiffre d'Affaires Coopérative Carbone (€ HT)",
            "% contrats avec obligation de résultat sur l'année",
            "Type de crédits (Séquestration / Réduction d'émissions)",
            'Risque / Provision pour risque (€ )',
            'Reprise pour provision  (€ )',
            'Pertes pour risques  (€ )',
            'Total contribution reçue et dûe au porteur de projet (€ HT)',
            "Total contribution versée au porteur du projet sur l'année (€ HT)",
        ];

        $partners = [];

        foreach ($this->getPartners() as $partner) {
            $partners[] = $partner->name;
        }

        $lastColumns = [
            'Total contributions fléchées',
            'Commission antenne sur cette année (HT €)',
            'Type de commission antenne',
            'Montant de la commission antenne global projet (HT €)',
        ];

        return [
            ...$returnArray,
            ...$partners,
            ...$partners,
            ...$lastColumns,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_PERCENTAGE_00,
            'G' => NumberFormat::FORMAT_CURRENCY_EUR,
            'H' => NumberFormat::FORMAT_CURRENCY_EUR,
            'E' => NumberFormat::FORMAT_CURRENCY_EUR,
            'I' => NumberFormat::FORMAT_CURRENCY_EUR,
            'J' => NumberFormat::FORMAT_CURRENCY_EUR,
            'K' => NumberFormat::FORMAT_CURRENCY_EUR,
            'L:'.self::getColumnLetter('M', $this->getPartners(count: true) * 2 - 1) => NumberFormat::FORMAT_CURRENCY_EUR,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $partnersCount = $this->getPartners(count: true);
        $partnersRangeArray = self::getColumnRange('K', $partnersCount * 2);
        $lastPartnerColumn = Arr::last($partnersRangeArray);

        return [

            // Header level 1 : Projet
            'B6:D6' => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'ffffc000',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],

            ],

            // Header level 1 : CA
            'E6' => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'ffffc000',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],

            // Header level 1 : Risque
            'E6:J6' => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'ff70ad47',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'], // Black border
                    ],
                ],
            ],

            'K6:'.$lastPartnerColumn.'6' => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'ff4472c4',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'], // Black border
                    ],
                ],
            ],

            'A7:ZZ7' => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

            'A8:ZZ8' => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

            // Partners group 1 title
            self::getCellFormatPartnerScope(1, 7, 'M') => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'], // Black border
                    ],
                ],
            ],

            // Partners group 2 title
            self::getCellFormatPartnerScope(2, 7, 'M') => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'], // Black border
                    ],
                ],
            ],
        ];
    }

    public function getColumnRange($startColumn, $offset)
    {
        $startIndex = self::getColumnIndex($startColumn);
        $columns = [];

        for ($i = 0; $i <= $offset; $i++) {
            $columns[] = self::getColumnLetter($startColumn, $i);
        }

        return $columns;
    }

    public function getColumnLetter($startColumn, $offset)
    {
        $resultingColumn = '';
        $currentColumnIndex = self::getColumnIndex($startColumn) + $offset;

        while ($currentColumnIndex > 0) {
            $currentColumnIndex--; // Adjust for 0-indexed calculation
            $resultingColumn = chr($currentColumnIndex % 26 + 65).$resultingColumn;
            $currentColumnIndex = (int) ($currentColumnIndex / 26);
        }

        return $resultingColumn;
    }

    public function getColumnIndex($columnLetter)
    {
        $columnLetter = strtoupper($columnLetter);
        $length = strlen($columnLetter);
        $index = 0;

        for ($i = 0; $i < $length; $i++) {
            $index *= 26;
            $index += ord($columnLetter[$i]) - 64; // 64 = ASCII code for 'A' - 1
        }

        return $index;
    }

    public function getCellFormatPartnerScope(int $scope = 1, int $line = 7, string $root = 'M'): string
    {
        $partnersCount = $this->getPartners(count: true);

        if ($scope == 1) {
            return $root.$line.':'.self::getColumnLetter('M', $partnersCount - 1).$line;
        }

        $rootColumn = self::getColumnLetter('M', $partnersCount);

        return $rootColumn.$line.':'.self::getColumnLetter($rootColumn, $partnersCount - 1).$line;
    }

    /**
     * @return int|Collection<Partner>
     */
    protected function getPartners(bool $count = false): int|Collection
    {
        if ($count) {
            return Partner::where('tenant_id', $this->tenant->id)->count();
        }

        return Partner::where('tenant_id', $this->tenant->id)->orderBy('name')->get();
    }

    // Get yearly donation for all the tenant
    public function getYearlyDonation(string $mode = 'HT'): float
    {
        if (! in_array($mode, ['HT', 'TTC'])) {
            return -1;
        }

        $getTTC = function (): float {
            return Donation::where('tenant_id', $this->tenant->id)
                ->whereYear('created_at', $this->year)
                ->sum('amount');
        };

        if ($mode == 'TTC') {
            return $getTTC();
        }

        // Mode is HT
        $projects = $this->tenant->projects()->has('donationSplits')->get();

        $htAmount = 0;

        /** @var Project $project */
        foreach ($projects as $project) {
            $projectAmount = $project->donationSplits()
                ->whereYear('created_at', $this->year)
                ->sum('amount');

            if ($project->subject_to_vat) {
                $htAmount += TVAHelper::getHT($projectAmount);
            } else {
                $htAmount += $projectAmount - (0.2 * $project->getTenantCommission());
            }
        }

        return $htAmount;
    }

    // Get donations for a given year for a given project
    public function getProjectYearlyDonation(Project $project, string $mode = 'HT', ?int $year = null): float
    {
        if (! in_array($mode, ['HT', 'TTC'])) {
            return -1;
        }

        if (! $year) {
            $year = $this->year;
        }

        $getTTC = function () use ($project, $year) {
            return $project->donationSplits()->whereYear('created_at', $year)->sum('amount');
        };

        if ($mode == 'TTC') {
            return $getTTC();
        }

        $projectAmount = $project->donationSplits()
            ->whereYear('created_at', $year)
            ->sum('amount');

        if ($project->subject_to_vat) {
            return TVAHelper::getHT($projectAmount);
        }

        // On applique seulement sur la commission affiliée pour cette année-là
        return $projectAmount - (0.2 * $this->getYearlyAmountProjectHolder(project: $project, year: $year, mode: 'affiliated'));
    }

    public function getProjectTenantCommissionYearly(Project $project, int $year): float
    {
        // Seulement la commission en pourcentage peut être renvoyée annuellement
        return match ($project->tenant_commission_type) {
            CommissionTypeEnum::Percentage => $this->getProjectYearlyDonation(project: $project, mode: 'HT', year: $year) * ($project->tenant_commission_percentage / 100),
            default => 0,
        };
    }

    // If a partner is given in the function we will return the amount for this year, if not the ca for the year
    public function getProjectRepartitionYearly(Project $project, ?Partner $partner = null): float
    {
        $firstDonation = $project->donationSplits()->min('created_at');
        $lastDonation = $project->donationSplits()->max('created_at');
        $lastPayment = $project->projectHolderPayments()->max('created_at');

        $firstDonation = new \DateTime($firstDonation);
        $lastDonation = new \DateTime($lastDonation);
        $lastPayment = new \DateTime($lastPayment);

        $interval = new \DateInterval('P1Y');

        $period = new \DatePeriod($firstDonation, $interval, max($lastDonation, $lastPayment));

        $years = [];

        foreach ($period as $date) {
            $years[$date->format('Y')] = [];
        }

        if (! in_array($lastDonation, $years)) {
            $years[$lastDonation->format('Y')] = [];
        }

        if ($lastPayment and ! in_array($lastPayment, $years)) {
            $years[$lastPayment->format('Y')] = [];
        }

        // Always percentage first
        $projectPartners = $project->projectPartners()->orderBy('commission_type', 'desc')->with('partner')->get();

        foreach ($years as $key => &$values) {
            $values['ca_ht'] = $this->getProjectYearlyDonation(project: $project, mode: 'HT', year: $key);
            $values['partners'] = $projectPartners->pluck(0, 'partner_id')->toArray();
        }

        foreach ($projectPartners as $projectPartner) {

            $projectPartner['remainsToBeAffiliated'] = match ($projectPartner->commission_type) {
                CommissionTypeEnum::Numerical => $projectPartner->commission_numerical,
                default => null,
            };

            foreach ($years as $key => &$values) {

                // Commission percentage
                if ($projectPartner->commission_type == CommissionTypeEnum::Percentage) {
                    $values['partners'][$projectPartner->partner_id] = $values['ca_ht'] * ($projectPartner->commission_percentage / 100);

                    continue;
                }

                // Commission numerical
                // On a déjà tout affilier
                if ($values['ca_ht'] <= array_sum($values['partners'])) {
                    $values['partners'][$projectPartner->partner_id] = 0;
                }

                if ($projectPartner['remainsToBeAffiliated'] == 0) {
                    $values['partners'][$projectPartner->partner_id] = 0;
                }

                if (($values['ca_ht'] - array_sum($values['partners'])) >= $projectPartner['remainsToBeAffiliated']) {
                    $affiliated = $projectPartner['remainsToBeAffiliated'];
                } else {
                    $affiliated = ($values['ca_ht'] - array_sum($values['partners']));
                }

                $values['partners'][$projectPartner->partner_id] = $affiliated;
                $projectPartner['remainsToBeAffiliated'] = $projectPartner['remainsToBeAffiliated'] - $affiliated;
            }
        }

        if ($partner) {
            return $years[$this->year]['partners'][$partner->id] ?? 0;
        }

        return $years[$this->year]['ca_ht'] - array_sum($years[$this->year]['partners']);
    }

    public function getRisk(Project $project): float
    {
        $risk1 = 0;

        if (! $project->is_audit_done and $project->credit_characteristics == CarbonCreditCharacteristicsEnum::Sequestration) {
            $risk1 = $this->getProjectRepartitionYearly(project: $project);
        }

        if (! $project->is_audit_done and $project->credit_characteristics == CarbonCreditCharacteristicsEnum::Avoidance) {
            $risk1 = $project->getTenantCommission() * 0.5;
        }

        $risk2 = $this->getContractsWithObligationToAchieveResults(project: $project, year: $this->year) * $this->getProjectYearlyDonation(project: $project, mode: 'HT', year: $this->year);

        $paymentsPartnersSumAmount = $project->projectPartners()->with('payments')->get()->pluck('payments')->collapse()->filter(function ($value) {
            return $value->created_at->year == $this->year;
        })->sum('amount');

        $sumAmountGiven = $project->projectHolderPayments()->whereYear('created_at', $this->year)->sum('amount') + $paymentsPartnersSumAmount;

        $risk3 = $sumAmountGiven * $this->getContractsWithObligationToAchieveResults(project: $project, year: $this->year);

        return max($risk1, $risk2, $risk3);
    }

    public function getYearlyAmountProjectHolder(Project $project, int $year, string $mode = 'amount_holder'): float
    {
        if ($project->tenant_commission_type == CommissionTypeEnum::Percentage) {
            return $this->getProjectYearlyDonation(project: $project, mode: 'HT', year: $year) - $this->getProjectTenantCommissionYearly(project: $project, year: $year);
        }

        $firstDonation = $project->donationSplits()->min('created_at');
        $lastDonation = $project->donationSplits()->max('created_at');
        $lastPayment = $project->projectHolderPayments()->max('created_at');

        $firstDonation = new \DateTime($firstDonation);
        $lastDonation = new \DateTime($lastDonation);
        $lastPayment = new \DateTime($lastPayment);

        $interval = new \DateInterval('P1Y');

        $period = new \DatePeriod($firstDonation, $interval, max($lastDonation, $lastPayment));

        $years = [];

        foreach ($period as $date) {
            $years[$date->format('Y')] = [];
        }

        if (! in_array($lastDonation, $years)) {
            $years[$lastDonation->format('Y')] = [];
        }

        if ($lastPayment and ! in_array($lastPayment, $years)) {
            $years[$lastPayment->format('Y')] = [];
        }

        $remainToBeAffiliated = $project->tenant_commission_numerical;

        foreach ($years as $key => &$values) {
            $values['ca_ht'] = $this->getProjectYearlyDonation(project: $project, mode: 'HT', year: $key);
            $values['affiliated'] = 0;
        }

        foreach ($years as $key => &$values) {

            // Si on a déjà affilié toute la commission
            if (collect($years)->sum('affiliated') >= $project->tenant_commission_numerical) {
                continue;
            }

            if ($values['ca_ht'] >= $remainToBeAffiliated) {
                $values['affiliated'] = $remainToBeAffiliated;
                $remainToBeAffiliated = 0;

                continue;
            }

            $values['affiliated'] = $values['ca_ht'];
            $remainToBeAffiliated -= $values['ca_ht'];
        }

        if ($mode = 'affiliated') {
            return $years[$year]['affiliated'] ?? 0;
        }

        return $this->getProjectYearlyDonation(project: $project, mode: 'HT', year: $year) - $years[$year]['affiliated'] ?? 0;
    }

    public function getContractsWithObligationToAchieveResults(Project $project, int $year): float
    {
        if (Arr::has($project->contracts_with_obligation_to_achieve_results, $year)) {
            return (float) $project->contracts_with_obligation_to_achieve_results[$year] / 100;
        }

        return 0;
    }

    public function getPlannedAuditYear(Project $project): float
    {
        if ($project->planned_audit_year) {
            return $project->planned_audit_year;
        }

        if ($project->start_at) {
            return $project->start_at->addYears(5)->format('Y');
        }

        return $project->created_at->addYears(5)->format('Y');
    }
}
