<?php

namespace App\Http\Controllers\Organizations;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateUserLinkController extends Controller
{
    public function __invoke(Request $request, Organization $organization, User $user)
    {
        $request->validate([
            'organization_type_link_id' => 'required',
        ]);

        DB::table('user_organizations')->where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->update([
                'organization_type_link_id' => $request->input('organization_type_link_id'),
            ]);

        ActivityHelper::push(
            performedOn: $organization,
            title: "Mise à jour du lien avec l'entité pour $user->name",
            url: route('organizations.show', ['organization' => $organization->slug])
        );

        return back();
    }
}
