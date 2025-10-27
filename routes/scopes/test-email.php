<?php 
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Project;
use App\Models\Organization; // Ajout de l'import Organization
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

// test message après contribution 
Route::middleware(['auth'])->get('/test-email-project-confirmation', function (Request $request) {
    $userEmailParam = $request->input('user');

    if (!$userEmailParam) {
        return response('Veuillez spécifier un utilisateur avec le paramètre ?user=email@example.com', 400);
    }

    $targetUser = User::where('email', $userEmailParam)->first();

    if (!$targetUser) {
        return response("Utilisateur non trouvé : " . htmlspecialchars($userEmailParam), 404);
    }

    // 3. (gardé pour la cohérence du layout de l'email)
    $tenant = Tenant::first();
    if (!$tenant) {
        $tenant = new Tenant([
            'name' => 'Tenant de Test (Défaut)',
            'primary_color' => '#3B82F6',
            'primary_color_text' => '#FFFFFF',
        ]);
    }

    $contributorEntity = $targetUser; 
    $contributorEntityType = User::class;
    $greetingName = $targetUser->first_name; 

    $userFirstOrganization = $targetUser->organizations()->first();

    if ($userFirstOrganization) {
        $orgHasProjectContributions = DonationSplit::whereNotNull('project_id')
            ->whereHas('donation', function ($query) use ($userFirstOrganization) {
                $query->where('related_type', Organization::class)
                      ->where('related_id', $userFirstOrganization->id);
            })->exists();

        if ($orgHasProjectContributions) {
            $contributorEntity = $userFirstOrganization;
            $contributorEntityType = Organization::class;
        }
    }

    $baseDonationSplitsQuery = DonationSplit::with(['project', 'donation.related'])
        ->whereNotNull('project_id') 
        ->whereHas('donation', function ($query) use ($contributorEntity, $contributorEntityType) {
            $query->where('related_type', $contributorEntityType)
                  ->where('related_id', $contributorEntity->id);
        });

    $viewData = [
        'tenant' => $tenant,
        'donationSplit' => null,
        'greetingName' => $greetingName,
    ];

    $projectInput = $request->input('project');
    $isProjectSpecificScenario = false;

    if ($projectInput !== null) {
        $lowerProjectInput = strtolower($projectInput);
        if ($projectInput !== '' && $lowerProjectInput !== '0' && $lowerProjectInput !== 'false') {
            $isProjectSpecificScenario = true;
        }
    }

    if ($isProjectSpecificScenario) {
        $projectContributionSplit = null;

        if (is_numeric($projectInput) && intval($projectInput) > 1) {
            $specificProjectIdToSearch = intval($projectInput);
            $projectContributionSplit = (clone $baseDonationSplitsQuery)
                ->where('project_id', $specificProjectIdToSearch)
                ->first();

            if (!$projectContributionSplit) {
                return response("L'entité contributrice (" . class_basename($contributorEntityType) . ": " . htmlspecialchars($contributorEntity->name ?? $contributorEntity->email) . ") n'a pas contribué au projet ID " . htmlspecialchars($projectInput) . ", ou cette contribution n'est pas trouvée.", 404);
            }
        } else {
            $projectContributionSplit = (clone $baseDonationSplitsQuery)->latest()->first();

            if (!$projectContributionSplit) {
                return response(
                    "L'entité contributrice (" . class_basename($contributorEntityType) . ": " . htmlspecialchars($contributorEntity->name ?? $contributorEntity->email) . ")" .
                    " n'a aucune contribution à un projet spécifique enregistrée pour simuler ce cas (demandé via project=" .
                    htmlspecialchars($projectInput) . ").", 404);
            }
        }

        $viewData['donationSplits'] = collect([$projectContributionSplit]);
        $viewData['donationSplit'] = $projectContributionSplit; 

    } else {
        $genericDonationForUserView = new Donation();
        $genericDonationForUserView->related_type = get_class($targetUser);
        $genericDonationForUserView->related_id = $targetUser->id;
        $genericDonationForUserView->setRelation('related', $targetUser);
        $dummySplit1 = new DonationSplit();
        $dummySplit1->setRelation('donation', $genericDonationForUserView);

        $dummySplit2 = new DonationSplit(); 
        $dummySplit2->setRelation('donation', $genericDonationForUserView);

        $viewData['donationSplits'] = collect([$dummySplit1, $dummySplit2]);
    }

    return view('emails.notifications.transactions.confirmation-payment', $viewData);
});