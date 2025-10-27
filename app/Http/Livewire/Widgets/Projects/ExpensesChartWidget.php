<?php

namespace App\Http\Livewire\Widgets\Projects;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Filament\Support\RawJs;

class ExpensesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Détails des dépenses';

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
        $expenses = collect($this->project->expenses);

        return [
            'datasets' => [
                [
                    'label' => 'Dépenses',
                    'data' => $expenses->pluck('amount_ht')->toArray(),
                    'backgroundColor' => $expenses->pluck('color')->toArray(),
                ],
            ],
            'labels' => $expenses->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
