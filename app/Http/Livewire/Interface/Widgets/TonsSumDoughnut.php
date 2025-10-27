<?php

namespace App\Http\Livewire\Interface\Widgets;

use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Segmentation;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\View\ViewException;

class TonsSumDoughnut extends ChartWidget
{
    protected static ?string $heading = 'Tonnage CO2 financé par contribution';

    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '250px';

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
                'display' => false,
            ],
            'x' => [
                'display' => false,
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

        $preparedData = [];

        foreach ($donationSplitsGrouped as $key => $values) {

            $segmentationName = $segmentations->where('id', $key)->first()?->name ?? 'Autre';
            $segmentationColor = $segmentations->where('id', $key)->first()?->chart_color ?? '#808080';

            $preparedData[$segmentationName] = [
                'value' => $values->sum('tonne_co2'),
                'color' => $segmentationColor,
            ];

        }

        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => collect($preparedData)->pluck('value')->toArray(),
                    'backgroundColor' => collect($preparedData)->pluck('color')->toArray(),
                ],
            ],
            'labels' => array_keys($preparedData),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
