<?php

use App\Models\Project;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    /**
     * Determine if the operation is being processed asynchronously.
     */
    protected bool $async = false;

    /**
     * The queue that the job will be dispatched to.
     */
    protected string $queue = 'default';

    /**
     * A tag name, that this operation can be filtered by.
     */
    protected ?string $tag = null;

    /**
     * Process the operation.
     */
    public function process(): void
    {
        $donationsToBeUpdated = [
            // 2021
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '100,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '83,33 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '2,0324', 'project' => 'Création d\'une forêt à Lanouaille', 'amount_split' => '100,00 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '94,20 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '78,50 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,7850', 'project' => 'La Forêt Bleue', 'amount_split' => '94,20 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '27,60 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '23,01 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,0359', 'project' => 'La Forêt Bleue', 'amount_split' => '27,60 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '0,80 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '0,66 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,0347', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '0,80 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '40,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '33,33 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '1,1493', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '40,00 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '112,40 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '93,67 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '3,1223', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '112,40 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '2,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '1,67 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,0506', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '2,00 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '2,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '1,67 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,0348', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '2,00 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '1,60 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '1,33 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,0047', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '1,60 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            // 2022
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '10,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '8,33 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,1893', 'project' => 'Création d\'une forêt à Saint-Martin-le-Pin (24)', 'amount_split' => '10,00 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '100,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '83,34 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,8334', 'project' => 'La Forêt Bleue', 'amount_split' => '100,00 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '2,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '1,67 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,0506', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '2,00 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '103,50 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '86,25 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '2,3311', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '103,50 €', 'date' => '', 'email' => '', 'payment_mode' => ''],
            ['type' => 'Entité', 'organization' => '', 'instance' => '', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '', 'to_be_updated_amount_ttc' => '4,00 €', 'current_amount_ht' => '', 'to_be_updated_amount_ht' => '3,33 €', 'current_tons_co2' => '', 'to_be_updated_tons_co2' => '0,0833', 'project' => 'Plantons les arbres têtards de demain dans le marais poitevin', 'amount_split' => '4,00 €', 'date' => '', 'email' => '', 'payment_mode' => '']
        ];

        $tenant = \App\Models\Tenant::first();
        $adminUser = \App\Models\User::where('email', 'eliott.baylot@gmail.com')->first();
        $organization = \App\Models\Organization::firstOrCreate([
            'name' => 'Rectification contributions',
            'created_by' => $adminUser->id
        ]);


        foreach ($donationsToBeUpdated as $donationToBeUpdated) {
            $project = Project::where('name', $donationToBeUpdated['project'])->first();

            // Update format values
            $donationToBeUpdated['amount_split'] = $this->formatNumberFloat(number: $donationToBeUpdated['amount_split']);
            $donationToBeUpdated['to_be_updated_amount_ttc'] = $this->formatNumberFloat(number: $donationToBeUpdated['to_be_updated_amount_ttc']);
            $donationToBeUpdated['current_amount_ht'] = $this->formatNumberFloat(number: $donationToBeUpdated['current_amount_ht']);
            $donationToBeUpdated['to_be_updated_amount_ht'] = $this->formatNumberFloat(number: $donationToBeUpdated['to_be_updated_amount_ht']);
            $donationToBeUpdated['current_tons_co2'] = $this->formatNumberFloat(number: $donationToBeUpdated['current_tons_co2']);
            $donationToBeUpdated['to_be_updated_tons_co2'] = $this->formatNumberFloat(number: $donationToBeUpdated['to_be_updated_tons_co2']);

            $donation = \App\Models\Donation::create([
                'related_type' => \App\Models\Organization::class,
                'related_id' => $organization->id,
                'amount' => $donationToBeUpdated['to_be_updated_amount_ttc'],
                'created_by' => $adminUser->id,
                'tenant_id' => $tenant->id,
                'is_donation_splits_full' => true
            ]);

            // Create a new carbon price at the date
            $carbonPrice = \App\Models\ProjectCarbonPrice::create([
                'price' => round($donationToBeUpdated['to_be_updated_amount_ht'] / $donationToBeUpdated['to_be_updated_tons_co2']),
                'created_by' => $adminUser->id,
                'project_id' => $project->id,
                'sync_with_tenant' => false,
                'start_at' => $donation->created_at,
                'end_at' => $donation->created_at->addMinute(),
            ]);

            \App\Models\DonationSplit::forceCreateQuietly([
                'donation_id' => $donation->id,
                'amount' => $donation->amount,
                'project_id' => $project->id,
                'tonne_co2' => $donationToBeUpdated['to_be_updated_tons_co2'],
                'project_carbon_price_id' => $carbonPrice->id,
                'split_by' => $adminUser->id
            ]);
        }
    }

    protected function formatNumberFloat(string $number) : float
    {
        return \Illuminate\Support\Str::of($number)
            ->replace(' ', '')
            ->replace(',', '.')
            ->replace(' €', '')
            ->toFloat();
    }
};
