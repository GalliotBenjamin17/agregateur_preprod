<?php

namespace App\Http\Controllers\Partners\Details;

use App\Enums\Models\PartnerProjectPayments\PaymentStateEnum;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerProject;
use Illuminate\Http\Request;

class ShowPartnerStatisticsController extends Controller
{
    public function __invoke(Request $request, Partner $partner)
    {
        $projectsCount = $partner->partnerProjects()->get()->unique('project_id')->count();
        $paymentsDoneAmount = PartnerProject::with([
            'payments' => function ($query) {
                return $query->where('payment_state', PaymentStateEnum::Sent);
            },
        ])->where('partner_id', $partner->id)->get()->sum('payments.amount');

        return view('app.partners.details.statistics')->with([
            'partner' => $partner,
            'projectsCount' => $projectsCount,
            'paymentsDoneAmount' => $paymentsDoneAmount,
        ]);
    }
}
