<?php

namespace App\Exports\ProjectFinancials;

use App\Exceptions\EmptyDonationsExportException;
use App\Exports\ProjectFinancials\Sheets\ProjectFinancialYearSheet;
use App\Models\DonationSplit;
use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProjectFinancialExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        public Tenant $tenant
    ) {
    }

    public function sheets(): array
    {
        $sheets = [];

        $firstDonation = DonationSplit::whereRelation('donation', 'tenant_id', '=', $this->tenant->id)->min('created_at');
        $lastDonation = DonationSplit::whereRelation('donation', 'tenant_id', '=', $this->tenant->id)->max('created_at');

        if (! $firstDonation or ! $lastDonation) {
            throw new EmptyDonationsExportException('Aucune contribution disponible.');
        }

        $firstDonation = new \DateTime($firstDonation);
        $lastDonation = new \DateTime($lastDonation);

        $interval = new \DateInterval('P1Y');

        $period = new \DatePeriod($firstDonation, $interval, $lastDonation);

        $years = [];

        foreach ($period as $date) {
            $years[] = $date->format('Y');
        }

        if (! in_array($lastDonation, $years)) {
            $years[] = $lastDonation->format('Y');
        }

        foreach ($years as $year) {
            $sheets[(string) $year] = new ProjectFinancialYearSheet(year: $year, tenant: $this->tenant);
        }

        return $sheets;
    }
}
