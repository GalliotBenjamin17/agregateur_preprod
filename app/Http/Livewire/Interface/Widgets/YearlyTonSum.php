<?php

namespace App\Http\Livewire\Interface\Widgets;

use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Segmentation;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class YearlyTonSum extends ChartWidget
{
    protected static ?string $heading = 'Contributions annuelles par type de projet';

    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '300px';

    public ?Organization $organization = null;

    public ?User $user = null;

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'bottom',
            ],
        ],
        'scales' => [
            'x' => [
                'stacked' => true,
                'title' => [
                    'display' => true,
                    'text' => 'Années'
                ]
            ],
            'y' => [
                'stacked' => true,
                'title' => [
                    'display' => true,
                    'text' => 'Tonnes CO2'
                ]
            ],
        ],
    ];

    protected function getData(): array
    {
        // Fetch donation splits with related projects
        $donationSplits = DonationSplit::with(['project', 'donation'])
            ->when($this->organization, function ($query) {
                return $query->whereRelation('donation', 'related_id', $this->organization->id)
                    ->whereRelation('donation', 'related_type', get_class($this->organization));
            })
            ->when($this->user, function ($query) {
                return $query->whereRelation('donation', 'related_id', $this->user->id)
                    ->whereRelation('donation', 'related_type', get_class($this->user));
            })->get();

        // Get all segmentations
        $segmentations = Segmentation::all();

        // Group donations by year and segmentation
        $groupedData = $donationSplits->groupBy([
            function ($split) {
                return $split->donation->created_at?->year ?? $split->donation->updated_at->year;
            },
            'project.segmentation_id'
        ]);

        // Prepare years array (for labels)
        $years = $groupedData->keys()->sort()->values();

        // Prepare datasets (one dataset per segmentation)
        $datasets = [];
        foreach ($segmentations as $segmentation) {
            $yearlyData = [];
            foreach ($years as $year) {
                $yearlyData[] = $groupedData->get($year, collect())
                    ->get($segmentation->id, collect())
                    ->sum('tonne_co2');
            }

            if (collect($yearlyData)->sum() == 0) {
                continue;
            }

            $datasets[] = [
                'label' => $segmentation->name,
                'data' => $yearlyData,
                'backgroundColor' => $segmentation->chart_color ?? '#808080',
                'borderColor' => $segmentation->chart_color ?? '#808080',  // Match background color
                'borderWidth' => 1, // You can also set this to 0 to completely remove the border
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $years->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
