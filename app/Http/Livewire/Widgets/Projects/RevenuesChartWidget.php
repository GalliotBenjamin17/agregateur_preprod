<?php

namespace App\Http\Livewire\Widgets\Projects;

use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class RevenuesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Détails des recettes';

    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '250px';

    public Project $project;

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                return label + ': ' + context.formattedValue + ' € HT';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        display: false
                    },
                    x: {
                        display: false
                    }
                }
            }
            JS);
    }

    protected function getData(): array
    {
        $revenues = collect($this->project->revenues);

        return [
            'datasets' => [
                [
                    'label' => 'Dépenses',
                    'data' => $revenues->pluck('amount_ht')->toArray(),
                    'backgroundColor' => $revenues->pluck('color')->toArray(),
                ],
            ],
            'labels' => $revenues->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
