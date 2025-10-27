<?php

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
            ['type' => 'Entité', 'organization' => 'Communauté d\'Agglomération La Rochelle', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '1 875,00 €', 'to_be_updated_amount_ttc' => '2 250,00 €', 'current_amount_ht' => '1 562,50 €', 'to_be_updated_amount_ht' => '1 875,00 €', 'current_tons_co2' => '62,5', 'to_be_updated_tons_co2' => '62,5', 'project' => 'Marais Poitevin', 'amount_split' => '1 875,00 €', 'date' => '4/5/21 22:00', 'email' => 'contributeurcda@gmail.com', 'payzen_id' => '01H2TB5T32VHPT33J7D11QVNKK', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Port Atlantique La Rochelle', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '5 000,00 €', 'to_be_updated_amount_ttc' => '6 000,00 €', 'current_amount_ht' => '4 166,67 €', 'to_be_updated_amount_ht' => '5 000,00 €', 'current_tons_co2' => '50', 'to_be_updated_tons_co2' => '50', 'project' => 'La Forêt Bleue', 'amount_split' => '5 000,00 €', 'date' => '12/14/21 23:00', 'email' => 'b.plisson@larochelle.port.fr', 'payzen_id' => '01H2TB41YE9NEN71TESGX6786S', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Port Atlantique La Rochelle', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '1 875,00 €', 'to_be_updated_amount_ttc' => '2 250,00 €', 'current_amount_ht' => '1 562,50 €', 'to_be_updated_amount_ht' => '1 875,00 €', 'current_tons_co2' => '50,68', 'to_be_updated_tons_co2' => '50', 'project' => 'Marais Poitevin', 'amount_split' => '1 875,00 €', 'date' => '12/14/21 23:00', 'email' => 'b.plisson@larochelle.port.fr', 'payzen_id' => '01H2TB5X9WXBD3ENZM9CG8QM4S', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Serda SAS', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '1 200,00 €', 'to_be_updated_amount_ttc' => '1 440,00 €', 'current_amount_ht' => '1 000,00 €', 'to_be_updated_amount_ht' => '1 200,00 €', 'current_tons_co2' => '12', 'to_be_updated_tons_co2' => '12', 'project' => 'La Forêt Bleue', 'amount_split' => '1 200,00 €', 'date' => '12/15/21 23:00', 'email' => 'jean.gauthier@serda.com', 'payzen_id' => '01H2TB40VHFSK1YHD9F7T6AR2C', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Serda SAS', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '360,00 €', 'to_be_updated_amount_ttc' => '432,00 €', 'current_amount_ht' => '300,00 €', 'to_be_updated_amount_ht' => '360,00 €', 'current_tons_co2' => '12', 'to_be_updated_tons_co2' => '12', 'project' => 'Marais Poitevin', 'amount_split' => '360,00 €', 'date' => '12/15/21 23:00', 'email' => 'jean.gauthier@serda.com', 'payzen_id' => '01H2TB5QMVTXYWZG324TXGRYKT', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Coop Charente Maritime Habitat', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '1 500,00 €', 'to_be_updated_amount_ttc' => '1 800,00 €', 'current_amount_ht' => '1 250,00 €', 'to_be_updated_amount_ht' => '1 500,00 €', 'current_tons_co2' => '15', 'to_be_updated_tons_co2' => '15', 'project' => 'La Forêt Bleue', 'amount_split' => '1 500,00 €', 'date' => '2/2/22 8:43', 'email' => 'communication@ccmh.fr', 'payzen_id' => '01H2TB431YQVMDKS1MB2Y9M818', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Smart', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '1 185,00 €', 'to_be_updated_amount_ttc' => '1 422,00 €', 'current_amount_ht' => '987,50 €', 'to_be_updated_amount_ht' => '1 185,00 €', 'current_tons_co2' => '26,93', 'to_be_updated_tons_co2' => '27', 'project' => 'Création d\'une forêt à Saint Martin le Pin', 'amount_split' => '1 185,00 €', 'date' => '3/25/22 14:27', 'email' => 'pberruet@euklead.com', 'payzen_id' => '01H2TB37DHXHDXDKXNCY96H5JV', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Smart', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '1 404,50 €', 'to_be_updated_amount_ttc' => '1 685,40 €', 'current_amount_ht' => '1 170,42 €', 'to_be_updated_amount_ht' => '1 404,50 €', 'current_tons_co2' => '31,92', 'to_be_updated_tons_co2' => '32', 'project' => 'Création d\'une forêt à Saint Martin le Pin', 'amount_split' => '1 404,50 €', 'date' => '3/25/22 14:29', 'email' => 'pberruet@euklead.com', 'payzen_id' => '01H2TB38RRPJ6BQMXV85SZH4T0', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'Raccourci Interactive Group', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '2 000,00 €', 'to_be_updated_amount_ttc' => '2 400,00 €', 'current_amount_ht' => '1 666,67 €', 'to_be_updated_amount_ht' => '2 000,00 €', 'current_tons_co2' => '20', 'to_be_updated_tons_co2' => '20', 'project' => 'La Forêt Bleue', 'amount_split' => '2 000,00 €', 'date' => '4/21/22 7:01', 'email' => 'contact@raccourci.fr', 'payzen_id' => '01H2TB489R5JFE8A3MGM7G1KWM', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
            ['type' => 'Entité', 'organization' => 'CRITT agro-alimentaire', 'instance' => 'Coopérative Carbone La Rochelle', 'to_be_updated' => 'OUI', 'current_amount_ttc' => '2 700,00 €', 'to_be_updated_amount_ttc' => '3 240,00 €', 'current_amount_ht' => '2 250,00 €', 'to_be_updated_amount_ht' => '2 700,00 €', 'current_tons_co2' => '90', 'to_be_updated_tons_co2' => '90', 'project' => 'Marais Poitevin', 'amount_split' => '2 700,00 €', 'date' => '5/19/22 20:26', 'email' => 'info@crittiaa.com', 'payzen_id' => '01H2TB65D7BA8FXP81Y8MNA937', '[payzen_order_id' => '', '[PAYZEN] - Order ID' => '', 'payment_mode' => 'bank_account'],
        ];

        $adminUser = \App\Models\User::where('email', 'eliott.baylot@gmail.com')->first();

        foreach ($donationsToBeUpdated as $donationToBeUpdated) {
            $donation = \App\Models\Donation::where('external_id', $donationToBeUpdated['payzen_id'])->first();
            $donationSplit = $donation->donationSplits()->first(); // Each donation has only one split

            // Update format values
            $donationToBeUpdated['amount_split'] = $this->formatNumberFloat(number: $donationToBeUpdated['amount_split']);
            $donationToBeUpdated['to_be_updated_amount_ttc'] = $this->formatNumberFloat(number: $donationToBeUpdated['to_be_updated_amount_ttc']);
            $donationToBeUpdated['current_amount_ht'] = $this->formatNumberFloat(number: $donationToBeUpdated['current_amount_ht']);
            $donationToBeUpdated['to_be_updated_amount_ht'] = $this->formatNumberFloat(number: $donationToBeUpdated['to_be_updated_amount_ht']);
            $donationToBeUpdated['current_tons_co2'] = $this->formatNumberFloat(number: $donationToBeUpdated['current_tons_co2']);
            $donationToBeUpdated['to_be_updated_tons_co2'] = $this->formatNumberFloat(number: $donationToBeUpdated['to_be_updated_tons_co2']);


            // Create a new carbon price at the date
            $carbonPrice = \App\Models\ProjectCarbonPrice::create([
                'price' => round($donationToBeUpdated['to_be_updated_amount_ht'] / $donationToBeUpdated['to_be_updated_tons_co2']),
                'created_by' => $adminUser->id,
                'project_id' => $donationSplit->project->id,
                'sync_with_tenant' => false,
                'start_at' => $donation->created_at,
                'end_at' => $donation->created_at->addMinute(),
            ]);

            // Update the amount of the donation and the split amount
            $donation->update([
                'amount' => $donationToBeUpdated['to_be_updated_amount_ttc'],
            ]);

            // Update the carbon_price_id and the tons on the donations
            $donationSplit->update([
                'amount' => $donationToBeUpdated['to_be_updated_amount_ttc'],
                'project_carbon_price_id' => $carbonPrice->id
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
