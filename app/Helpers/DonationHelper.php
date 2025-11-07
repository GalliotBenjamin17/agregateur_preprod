<?php

namespace App\Helpers;

use App\Exceptions\DonationSplitAmountIsNullException;
use App\Exceptions\MoreDonationSplitAmountThanExpectedException;
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Project;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
 

class DonationHelper
{
    /**
     * @throws DonationSplitAmountIsNullException
     * @throws MoreDonationSplitAmountThanExpectedException
     */
    public static function buildSplit(Donation $donation, array $splits, ?User $splitBy = null): void
    {
        $sum = collect($splits)->sum('data.amount');

        $donation->load([
            'donationSplits',
        ]);

        if ($donation->donationSplits()->onlyParents()->sum('amount') + $sum > $donation->amount) {
            throw new MoreDonationSplitAmountThanExpectedException();
        }

        foreach ($splits as $split) {
            $projectId = $split['data']['project_id'];

            $project = Project::with([
                'childrenProjects' => [
                    'activeCarbonPrice',
                ],
                'activeCarbonPrice',
            ])->findOrFail($projectId);

            try {
                $amount = self::getAmount(project: $project, amount: $split['data']['amount']);
            } catch (DonationSplitAmountIsNullException $exception) {
                throw new DonationSplitAmountIsNullException();
            }

            //dd($amount, $amount / TVAHelper::getTTC($project->activeCarbonPrice->price), $project->activeCarbonPrice->price, TVAHelper::getTTC($project->activeCarbonPrice->price));

            $donationSplit = DonationSplit::create([
                'donation_id' => $donation->id,
                'amount' => $amount,
                'split_by' => $splitBy?->id ?? request()->user()->id,
                'project_id' => $project->id,
                'project_carbon_price_id' => $project->activeCarbonPrice->id,
                'tonne_co2' => $amount / TVAHelper::getTTC($project->activeCarbonPrice->price),
            ]);

            $project->touch('updated_at');

            if (\Arr::has($split, 'data.sub_project_id')) {
                $subProject = $project->childrenProjects->where('id', $split['data']['sub_project_id'])->first();

                try {
                    $subAmount = self::getAmount(project: $subProject, amount: $amount);
                } catch (DonationSplitAmountIsNullException $exception) {
                    throw new DonationSplitAmountIsNullException();
                }

                $donationSplitSubProject = DonationSplit::create([
                    'donation_id' => $donation->id,
                    'donation_split_id' => $donationSplit->id,
                    'amount' => $subAmount,
                    'split_by' => request()->user()->id,
                    'project_id' => $subProject->id,
                    'project_carbon_price_id' => $subProject->activeCarbonPrice->id,
                    'tonne_co2' => $subAmount / TVAHelper::getTTC($subProject->activeCarbonPrice->price),
                ]);

                $subProject->touch('updated_at');
            }
        }

        $donation->load([
            'donationSplits',
        ]);

        if ($donation->isSplitsFull()) {
            $donation->update([
                'is_donation_splits_full' => true,
            ]);
        }
    }

    /**
     * @throws DonationSplitAmountIsNullException
     * @throws MoreDonationSplitAmountThanExpectedException
     */
    public static function buildSplitOfSplit(DonationSplit $donationSplit, Project $project, array $split): void
    {
        $donationSplit->load([
            'childrenSplits',
        ]);

        $project->load([
            'activeCarbonPrice',
        ]);

        if ($donationSplit->childrenSplits()->sum('amount') + $split['amount'] > $donationSplit->amount) {
            throw new MoreDonationSplitAmountThanExpectedException();
        }

        $subProject = Project::findOrFail($split['project_id']);

        try {
            $amount = self::getAmount(project: $subProject, amount: $split['amount']);
        } catch (DonationSplitAmountIsNullException $exception) {
            throw new DonationSplitAmountIsNullException();
        }

        $donationSplitSubProject = DonationSplit::create([
            'donation_id' => $donationSplit->donation_id,
            'donation_split_id' => $donationSplit->id,
            'project_id' => $split['project_id'],
            'amount' => $amount,
            'split_by' => request()->user()->id,
            'project_carbon_price_id' => $project->activeCarbonPrice->id,
            'tonne_co2' => $amount / TVAHelper::getTTC($project->activeCarbonPrice->price),
        ]);
    }

    /**
     * @return string
     */
    public static function generateCertificate(Donation $donation)
    {
        $donation->load([
            'related',
            'tenant',
        ]);

        $donationsSplits = $donation->donationSplits()
            ->with([
                'project.segmentation', 
                'project.methodForm.methodFormGroup', 
                'project.certification',
                'projectCarbonPrice', 
            ])
            ->withCount('childrenSplits')
            ->get();

        $displayedDonationSplits = collect();

        foreach ($donationsSplits as $donationsSplit) {
            if ($donationsSplit->children_splits_count == 0) {
                $displayedDonationSplits->push($donationsSplit);
            }
        }

        $pdf = Pdf::loadView('pdfs.donation-certificate', [
            'donation' => $donation,
            'donationSplits' => $displayedDonationSplits,
            'related' => $donation->related,
            'tenant' => $donation->tenant,
        ]);

        $filePath = "/certificates/donations/{$donation->id}-".now()->format('d-m-Y').'.pdf';

        $path = $pdf->save(storage_path('app/public').$filePath);

        $publicPath = '/storage'.$filePath;

        $donation->update([
            'certificate_pdf_path' => $publicPath,
            'certificate_pdf_generated_at' => now(),
        ]);

        return $publicPath;
    }

    /**
     * @throws DonationSplitAmountIsNullException
     */
    public static function getAmount(Project $project, float $amount): float
    {
        $checkedAmount = match ($project->canAffiliateAmount($amount)) {
            true => $amount,
            false => $project->getDonationAmountRemaining()
        };

        if ($checkedAmount < 0) {
            throw new DonationSplitAmountIsNullException();
        }

        return $checkedAmount;
    }
}
