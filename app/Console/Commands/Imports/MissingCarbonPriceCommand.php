<?php

namespace App\Console\Commands\Imports;

use App\Models\Project;
use App\Services\Models\ProjectCarbonPriceService;
use Illuminate\Console\Command;

class MissingCarbonPriceCommand extends Command
{
    protected $signature = 'imports:missing-carbon-prices';

    protected $description = 'Command description';

    public function handle(): void
    {
        foreach (Project::doesntHave('carbonPrices')->get() as $project) {
            $priceCarbonPriceService = new ProjectCarbonPriceService(project: $project);
            $priceCarbonPrice = $priceCarbonPriceService->storeProjectCarbonPriceService();
        }
    }
}
