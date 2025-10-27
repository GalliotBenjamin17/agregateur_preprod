<?php

namespace App\Http\Livewire\Interface\Widgets;

use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Segmentation;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CumulativeTonsPerYearBarChart extends ChartWidget
{
    protected static ?string $heading = 'Vos contributions par année et type de projet';

    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '320px';

    public ?Organization $organization = null;

    public ?User $user = null;

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
        $donationSplits = DonationSplit::with('project')
            ->when($this->organization, function ($query) {
                return $query->whereRelation('donation', 'related_id', $this->organization->id)
                    ->whereRelation('donation', 'related_type', get_class($this->organization));
            })
            ->when($this->user, function ($query) {
                return $query->whereRelation('donation', 'related_id', $this->user->id)
                    ->whereRelation('donation', 'related_type', get_class($this->user));
            })->get();

        $donationSplits = $donationSplits->filter(function(DonationSplit $donationSplit) {
            // If this is a sub-split (has a parent), skip it as we only want to count
            // either the parent project or its direct sub-projects, not both
            if ($donationSplit->donation_split_id !== null) {
                return true;
            }

            // Check if this donation split has any sub-splits
            $hasChildSplits = $donationSplit->childrenSplits()->exists();

            if ($hasChildSplits) {
                // If it has sub-splits, we'll skip this one and count the children instead
                return false;
            }

            // Count this split if it's a top-level split with no children
            return true;
        })->each(function (DonationSplit &$donationSplit) {
            if (!$donationSplit->project->segmentation_id) {
                $donationSplit->project->segmentation_id = $donationSplit->parent->project->segmentation_id;
            }
        });

        $donationSplitsGrouped = $donationSplits->groupBy('project.segmentation_id');

        $segmentations = Segmentation::whereIn('id', $donationSplitsGrouped->keys())->get();

        $datasets = [];
        $years = [];

        $mandatoryYears = $donationSplits->pluck('created_at')->map(function ($item) {
            return (int) $item->format('Y');
        })->unique()->values()->toArray();

        foreach ($donationSplitsGrouped as $key => $values) {

            $perYear = collect($values)
                ->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('Y');
                });

            $yearlyData = [];

            foreach ($mandatoryYears as $mandatoryYear) {
                $yearlyData[(string) $mandatoryYear] = collect($perYear[$mandatoryYear] ?? [])->sum('tonne_co2');
            }

            $segmentationName = $segmentations->where('id', $key)->first()?->name ?? 'Autre';
            $segmentationColor = $segmentations->where('id', $key)->first()?->chart_color ?? '#808080';

            $years = [
                ...$years,
                ...array_keys($yearlyData),
            ];

            $datasets[] = [
                'data' => array_values($yearlyData),
                'backgroundColor' => $segmentationColor.'40',
                'borderColor' => $segmentationColor,
                'label' => $segmentationName,
            ];

        }

        return [
            'datasets' => $datasets,
            'labels' => collect($years)->values()->unique(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
