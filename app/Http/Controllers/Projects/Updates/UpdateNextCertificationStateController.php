<?php

namespace App\Http\Controllers\Projects\Updates;

use App\Enums\Models\Projects\CertificationStateEnum;
use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class UpdateNextCertificationStateController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $currentState = $project->certification_state;

        // 1 => 'notified',
        // 2 => 'pending_evaluation',
        // 3 => 'evaluated',
        // 4 => 'certified',
        // 5 => 'verified',
        // 6 => 'approved',

        $project->update([
            'certification_state' => match ($currentState) {
                CertificationStateEnum::Notified, CertificationStateEnum::PendingEvaluation => 'evaluated',
                CertificationStateEnum::Evaluated => 'certified',
                CertificationStateEnum::Certified => 'verified',
                CertificationStateEnum::Verified, CertificationStateEnum::Approved => 'approved',
                default => $project->certification_state
            },
        ]);

        \Session::flash('success', 'Le statut de la labellisation a été mis à jour.');

        ActivityHelper::push(
            performedOn: $project,
            title: "Mise à jour de l'avancement de la labellisation",
            url: route('projects.show.details', ['project' => $project])
        );

        return back();
    }
}
