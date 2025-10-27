<?php

namespace App\Console\Commands\Imports;

use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Project;
use App\Models\ProjectCarbonPrice;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Models\ProjectCarbonPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportPaymentssCommand extends Command
{
    protected $signature = 'imports:payments';

    protected $description = 'Command description';

    public function handle(): void
    {
        Donation::whereNotNull('id')->delete();
        DonationSplit::whereNotNull('id')->delete();
        ProjectCarbonPrice::whereNotNull('id')->delete();

        $defaultCreatedBy = User::first();

        $tenant = Tenant::first();

        $collection = (new FastExcel)->import(public_path('imports/payments.xlsx'));

        foreach ($collection as $row) {

            $model = match ($row['id_user'] == '') {
                true => Organization::where('old_id', $row['id_orga'])->first(),
                false => User::where('old_id', $row['id_user'])->first(),
            };

            $project = Project::where('old_id', $row['id_projet'])->first();

            $carbonPrice = ProjectCarbonPrice::firstOrCreate([
                'project_id' => $project->id,
                'price' => TVAHelper::getHT($row['prix_ton']),
            ], [
                'start_at' => $row['created_at'],
                'end_at' => $row['created_at'],
                'sync_with_tenant' => false,
                'created_by' => $project->createdBy->id,
            ]);

            $donation = Donation::create([
                'tenant_id' => $tenant->id,
                'related_type' => get_class($model),
                'related_id' => $model->id,
                'source' => 'bank_account',
                'external_id' => Str::ulid(),
                'amount' => $row['amount'],
                'created_at' => $row['created_at'],
                'created_by' => match (get_class($model) == User::class) {
                    true => $model->id,
                    false => $model->users()->first()?->id ?? $defaultCreatedBy->id
                },
            ]);

            $donationSplit = DonationSplit::create([
                'donation_id' => $donation->id,
                'amount' => $donation->amount,
                'split_by' => match (get_class($model) == User::class) {
                    true => $model->id,
                    false => $model->users()->first()?->id ?? $defaultCreatedBy->id
                },
                'created_at' => $row['created_at'],
                'project_id' => $project->id,
                'project_carbon_price_id' => $carbonPrice->id,
                'tonne_co2' => $donation->amount / $carbonPrice->price,
            ]);
        }

        foreach (Project::has('carbonPrices')->get() as $project) {
            $prices = $project->carbonPrices()->with('donationsSplit')->get();

            foreach ($prices as $price) {
                $splitsSorted = $price->donationsSplit->sortBy('created_at');

                if (count($splitsSorted) > 0) {
                    $price->update([
                        'start_at' => $splitsSorted->first()->created_at,
                        'end_at' => $splitsSorted->last()->created_at,
                    ]);
                }
            }

            $prices->sortByDesc('created_at')->first()->update([
                'end_at' => null,
            ]);
        }

        foreach (Project::doesntHave('carbonPrices')->get() as $project) {
            $priceCarbonPriceService = new ProjectCarbonPriceService(project: $project);
            $priceCarbonPrice = $priceCarbonPriceService->storeProjectCarbonPriceService();
        }

    }
}
