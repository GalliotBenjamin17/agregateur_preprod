<?php

namespace App\Http\Livewire\Interface\Widgets;

use Akaunting\Setting\Support\Arr;
use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Segmentation;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SpreadTonsPerYearLineChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution de la séquestration carbone du projet';

    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '320px';

    public ?Organization $organization = null;

    public ?User $user = null;

    public Project $project;

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
            ],
        ],
        'scales' => [
            'y' => [
                'display' => true,
                'ticks' => [
                    'stepSize' => 10,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Tonnes CO2',
                ],
            ],
            'x' => [
                'display' => true,
            ],
        ],
    ];

    protected function getData(): array
    {
        // Afficher seulement les données du projet actuel
        $projectsIds = [$this->project->id];

        $donationSplits = DonationSplit::with('project')
            ->whereIn('project_id', $projectsIds)
            ->when($this->organization, function ($query) {
                return $query->whereRelation('donation', 'related_id', $this->organization->id)
                    ->whereRelation('donation', 'related_type', get_class($this->organization));
            })
            ->when($this->user, function ($query) {
                return $query->whereRelation('donation', 'related_id', $this->user->id)
                    ->whereRelation('donation', 'related_type', get_class($this->user));
            })->get();

        // Pour les projets enfants, utiliser la segmentation du parent
        $segmentationId = $this->project->segmentation_id ?? $this->project->parentProject?->segmentation_id;
        
        // Grouper par la segmentation héritée
        $donationSplitsGrouped = collect([$segmentationId => $donationSplits]);

        $segmentations = Segmentation::whereIn('id', [$segmentationId])->get();

        $datasets = [];
        $years = [];

        foreach ($donationSplitsGrouped as $key => $values) {
            $segmentation = $segmentations->where('id', $key)->first();

            if (! $segmentation) {
                continue;
            }

            $spreadYears = (int) $segmentation->chart_spread_years;

            $perYear = collect($values)
                ->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('Y');
                });

            $yearlyData = [];
            $cumulativeTotal = 0; // Keep track of the running total

            // First, calculate the annual contributions for each year
            foreach ($perYear as $year => $splits) {
                foreach ($splits as $split) {
                    $amountPerYear = $split->tonne_co2 / $spreadYears;

                    // Distribute the per-year amount over the spread years
                    foreach (range($year, $year + $spreadYears - 1) as $spreadYear) {
                        $spreadYearString = (string) $spreadYear;

                        if (!isset($yearlyData[$spreadYearString])) {
                            $yearlyData[$spreadYearString] = 0;
                        }

                        $yearlyData[$spreadYearString] += $amountPerYear;
                    }
                }
            }

            // Sort the years to ensure we accumulate in chronological order
            ksort($yearlyData);

            // Convert to cumulative values
            $cumulativeData = [];
            foreach ($yearlyData as $year => $value) {
                $cumulativeTotal += $value;
                $cumulativeData[$year] = $cumulativeTotal;
            }

            $years = array_merge($years, array_keys($cumulativeData));

            $datasets[] = [
                'data' => array_values($cumulativeData),
                'label' => $segmentation?->name ?? 'Autre',
                // Uncomment these if you need them
                // 'backgroundColor' => $segmentation?->chart_color ?? '#808080'.'40',
                // 'borderColor' => $segmentation?->chart_color ?? '#808080',
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => collect($years)->values()->unique()->sort()->values(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
